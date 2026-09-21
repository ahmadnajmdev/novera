<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    public function run(): void
    {
        $form = Form::updateOrCreate(
            ['key' => 'enquiry'],
            [
                'name' => 'Project enquiry',
                'submit_label' => ['en' => 'Start Your Project'],
                'success_message' => ['en' => 'Thank you — we have your enquiry and will be in touch shortly.'],
                'notify_email' => 'info@noverainteriors.com',
                'store_submissions' => true,
            ],
        );

        $form->allFields()->delete();

        $fields = [
            [
                'key' => 'name',
                'type' => 'text',
                'label' => ['en' => 'Name'],
                'placeholder' => ['en' => 'Full name'],
                'is_required' => true,
            ],
            [
                'key' => 'phone',
                'type' => 'text',
                'label' => ['en' => 'Phone'],
                'placeholder' => ['en' => '+964 750 000 0000'],
                'is_required' => true,
            ],
            [
                'key' => 'email',
                'type' => 'email',
                'label' => ['en' => 'Email'],
                'placeholder' => ['en' => 'you@email.com'],
                'is_required' => true,
            ],
            [
                'key' => 'project_type',
                'type' => 'select',
                'label' => ['en' => 'Project Type'],
                'options' => [
                    ['value' => 'residence', 'label' => ['en' => 'Private residence']],
                    ['value' => 'apartment', 'label' => ['en' => 'Apartment / penthouse']],
                    ['value' => 'hospitality', 'label' => ['en' => 'Hospitality']],
                    ['value' => 'commercial', 'label' => ['en' => 'Commercial']],
                    ['value' => 'single_room', 'label' => ['en' => 'Single room / concept']],
                ],
                'is_required' => false,
            ],
            [
                'key' => 'message',
                'type' => 'textarea',
                'label' => ['en' => 'Message'],
                'placeholder' => ['en' => 'Rooms, area, timeline, anything already decided'],
                'rows' => 4,
                'is_required' => false,
            ],
        ];

        foreach ($fields as $sort => $field) {
            FormField::create(array_merge($field, [
                'form_id' => $form->id,
                'sort' => $sort,
                'is_visible' => true,
            ]));
        }
    }
}
