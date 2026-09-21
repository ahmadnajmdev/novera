<div>
    @if ($submitted)
        <div class="nv-form__notice" style="margin-top:30px" role="status">
            {{ nv_tr($form, 'success_message') }}
        </div>
    @else
        <form wire:submit="submit" style="margin-top:30px">
            {{-- Honeypot --}}
            <div aria-hidden="true" style="position:absolute;left:-9999px">
                <label>Company<input type="text" wire:model="company" tabindex="-1" autocomplete="off"></label>
            </div>

            @foreach ($form->fields as $field)
                @php $error = $errors->first('values.'.$field->key); @endphp
                <label class="nv-field {{ $error ? 'nv-field--invalid' : '' }}">
                    <span class="nv-field__label">{{ nv_tr($field, 'label') }}</span>

                    @if ($field->type === 'textarea')
                        <textarea rows="{{ $field->rows ?? 4 }}"
                                  wire:model="values.{{ $field->key }}"
                                  placeholder="{{ nv_tr($field, 'placeholder') }}"></textarea>
                    @elseif ($field->type === 'select')
                        <select wire:model="values.{{ $field->key }}">
                            <option value="">{{ nv_tr($field, 'placeholder') }}</option>
                            @foreach ($field->optionList($locale ?? app()->getLocale()) as $value => $label)
                                <option value="{{ $value }}">{{ nv_t($label) }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="{{ $field->type === 'email' ? 'email' : 'text' }}"
                               wire:model="values.{{ $field->key }}"
                               placeholder="{{ nv_tr($field, 'placeholder') }}">
                    @endif

                    @if ($error)<span class="nv-field__error">{{ $error }}</span>@endif
                </label>
            @endforeach

            <button type="submit" class="nv-btn nv-btn--solid" style="margin-top:36px" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">{{ nv_tr($form, 'submit_label') }}</span>
                <span wire:loading wire:target="submit">@t('Sending…')</span>
            </button>
        </form>
    @endif
</div>
