<?php

namespace App\Providers;

use App\Support\CacheFlusher;
use App\Support\Settings;
use App\Support\Site;
use App\Support\SiteTranslator;
use App\Support\Urls;
use App\Livewire\ConceptTabs;
use App\Livewire\EnquiryForm;
use App\Livewire\MaterialIndex;
use App\Livewire\ProjectIndex;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class);
        $this->app->singleton(SiteTranslator::class);
        $this->app->singleton(Site::class);
        $this->app->singleton(Urls::class);
    }

    public function boot(): void
    {
        /**
         * @t('Some English source') renders a dictionary-translated string.
         * Keeps templates authored in English while the CMS holds every
         * other locale.
         */
        Blade::directive('t', fn (string $expression) => "<?php echo e(nv_t({$expression})); ?>");
        Blade::directive('traw', fn (string $expression) => "<?php echo nv_t({$expression}); ?>");

        // Registered explicitly rather than by discovery so the tag names used
        // in section templates are stable regardless of Livewire's defaults.
        Livewire::component('material-index', MaterialIndex::class);
        Livewire::component('project-index', ProjectIndex::class);
        Livewire::component('concept-tabs', ConceptTabs::class);
        Livewire::component('enquiry-form', EnquiryForm::class);

        CacheFlusher::register();
    }
}
