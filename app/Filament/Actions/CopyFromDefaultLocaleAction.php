<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;

/**
 * Fills the active locale's translatable fields with the default locale's
 * values (Portuguese), so a translation starts from the original blocks and
 * reuses its uploaded images. Nothing is persisted until the page is saved.
 *
 * Relies on the lara-zeus Translatable page traits, which keep the locales
 * that aren't on screen in $otherLocaleData.
 */
class CopyFromDefaultLocaleAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'copyFromDefaultLocale';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('admin.actions.copy_from_default.label'));
        $this->icon(Heroicon::OutlinedLanguage);
        $this->color('gray');
        $this->requiresConfirmation();
        $this->modalDescription(__('admin.actions.copy_from_default.description'));

        $this->visible(function (Page $livewire): bool {
            $defaultLocale = $livewire::getResource()::getDefaultTranslatableLocale();

            return $livewire->activeLocale !== $defaultLocale
                && filled($livewire->otherLocaleData[$defaultLocale] ?? null);
        });

        $this->action(function (Page $livewire): void {
            $resource = $livewire::getResource();
            $translatableAttributes = $resource::getTranslatableAttributes();
            $form = $livewire->getSchema('form');

            // Raw state: the target locale is usually still empty and would fail validation.
            $form->fill([
                ...Arr::except($form->getRawState(), $translatableAttributes),
                ...Arr::only(
                    $livewire->otherLocaleData[$resource::getDefaultTranslatableLocale()],
                    $translatableAttributes,
                ),
            ]);

            Notification::make()
                ->title(__('admin.actions.copy_from_default.done'))
                ->success()
                ->send();
        });
    }
}
