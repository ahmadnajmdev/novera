<?php

namespace App\Filament\Support;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

/**
 * The collapsed drawer at the bottom of a form holding the plumbing.
 *
 * Reference names and sort positions are real, but they are not what someone
 * came to the screen to do. Out of the way and filled in automatically, they
 * stop being the first thing a new editor has to solve.
 */
class Advanced
{
    /** @param  array<int, mixed>  $extra */
    public static function section(array $extra = [], bool $lockKey = false): Section
    {
        return Section::make('Advanced')
            ->description('Filled in for you. You will rarely need to open this.')
            ->collapsed()
            ->schema([
                TextInput::make('key')
                    ->label('Reference name')
                    ->helperText('How menus and page sections point at this. '
                        .'Leave it empty and one is made from the name.')
                    ->unique(ignoreRecord: true)
                    ->disabled($lockKey)
                    ->rule('regex:/^[a-z0-9\-_.]*$/')
                    ->validationMessages([
                        'regex' => 'Use lower-case letters, numbers and hyphens only.',
                    ]),
                ...$extra,
            ])
            ->columns(2);
    }

    public static function position(): TextInput
    {
        return TextInput::make('sort')
            ->label('Position in lists')
            ->helperText('Lower numbers come first.')
            ->numeric()
            ->default(0);
    }
}
