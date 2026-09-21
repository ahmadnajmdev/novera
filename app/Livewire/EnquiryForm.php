<?php

namespace App\Livewire;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

/**
 * Renders and validates whichever form the CMS points at — fields, labels,
 * placeholders, options and validation all come from the database.
 */
class EnquiryForm extends Component
{
    public string $formKey = 'enquiry';

    /** @var array<string, string|null> */
    public array $values = [];

    public bool $submitted = false;

    /** Honeypot: real people leave it empty. */
    public string $company = '';

    public function mount(): void
    {
        foreach ($this->form()->fields as $field) {
            $this->values[$field->key] ??= '';
        }
    }

    protected function form(): Form
    {
        return Form::with('fields')->where('key', $this->formKey)->firstOrFail();
    }

    protected function rules(): array
    {
        $rules = [];

        foreach ($this->form()->fields as $field) {
            $rules['values.'.$field->key] = $field->validationRules();
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        $attributes = [];

        foreach ($this->form()->fields as $field) {
            $attributes['values.'.$field->key] = mb_strtolower(nv_tr($field, 'label'));
        }

        return $attributes;
    }

    public function submit(): void
    {
        $form = $this->form();

        if (filled($this->company)) {
            $this->submitted = true;

            return;
        }

        $this->validate();

        if ($form->store_submissions) {
            FormSubmission::create([
                'form_id' => $form->id,
                'payload' => $this->values,
                'locale' => app()->getLocale(),
                'ip' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 512),
            ]);
        }

        if (filled($form->notify_email)) {
            $this->notify($form);
        }

        $this->submitted = true;
    }

    protected function notify(Form $form): void
    {
        $lines = collect($form->fields)
            ->map(fn ($field) => nv_tr($field, 'label').': '.($this->values[$field->key] ?? '—'))
            ->implode("\n");

        try {
            Mail::raw($lines, function ($message) use ($form) {
                $message->to($form->notify_email)->subject('New enquiry — '.$form->name);
            });
        } catch (\Throwable $exception) {
            // A mail outage must not lose the enquiry; it is already stored.
            report($exception);
        }
    }

    public function render()
    {
        return view('livewire.enquiry-form', ['form' => $this->form()]);
    }
}
