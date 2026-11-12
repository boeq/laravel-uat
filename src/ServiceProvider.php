<?php

declare(strict_types=1);

namespace Boeq\Uat;

use Boeq\Uat\Actions\Access\ResolveTester;
use Boeq\Uat\Actions\Feedback\ResolveFeedback;
use Boeq\Uat\Actions\Scenarios\ChangeScenarioStatus;
use Boeq\Uat\Actions\Scenarios\ListScenariosForTester;
use Boeq\Uat\Actions\Scenarios\SaveScenario;
use Boeq\Uat\Actions\Scenarios\SaveTranslation;
use Boeq\Uat\Actions\Sessions\AcceptSession;
use Boeq\Uat\Actions\Sessions\CompleteSession;
use Boeq\Uat\Actions\Sessions\JudgeStep;
use Boeq\Uat\Actions\Sessions\StartSession;
use Boeq\Uat\Actions\Tokens\CreateApiToken;
use Boeq\Uat\Commands\Tokens\CreateTokenCommand;
use Boeq\Uat\Commands\Tokens\RevokeTokenCommand;
use Boeq\Uat\Http\Middleware\AcceptJson;
use Boeq\Uat\Http\Middleware\AuthenticateApiToken;
use Boeq\Uat\Http\Middleware\AuthorizeTester;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Policies\ScenarioPolicy;
use Boeq\Uat\Policies\SessionPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this
            ->registerConfig()
            ->registerActions();
    }

    public function boot(): void
    {
        $this
            ->bootConfig()
            ->bootMigrations()
            ->bootTranslations()
            ->bootViews()
            ->bootPolicies()
            ->bootRoutes()
            ->bootCommands()
            ->bootSkill();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/uat.php', 'uat');

        return $this;
    }

    protected function registerActions(): static
    {
        ResolveTester::bind();

        SaveScenario::bind();
        SaveTranslation::bind();
        ChangeScenarioStatus::bind();
        ListScenariosForTester::bind();

        StartSession::bind();
        JudgeStep::bind();
        AcceptSession::bind();
        CompleteSession::bind();

        ResolveFeedback::bind();

        CreateApiToken::bind();

        return $this;
    }

    protected function bootConfig(): static
    {
        if (! $this->app->runningInConsole()) {
            return $this; // @codeCoverageIgnore
        }

        $this->publishes([
            __DIR__.'/../config/uat.php' => config_path('uat.php'),
        ], 'config');

        return $this;
    }

    protected function bootMigrations(): static
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        return $this;
    }

    protected function bootTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'uat');

        if (! $this->app->runningInConsole()) {
            return $this; // @codeCoverageIgnore
        }

        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/uat'),
        ], 'lang');

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'uat');

        Blade::componentNamespace('Boeq\\Uat\\Components', 'uat');

        if (! $this->app->runningInConsole()) {
            return $this; // @codeCoverageIgnore
        }

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/uat'),
        ], 'views');

        return $this;
    }

    protected function bootPolicies(): static
    {
        Gate::policy(Scenario::class, ScenarioPolicy::class);
        Gate::policy(Session::class, SessionPolicy::class);

        return $this;
    }

    protected function bootRoutes(): static
    {
        Route::prefix(config()->string('uat.api.prefix'))
            ->middleware([AcceptJson::class, ...config()->array('uat.api.middleware'), AuthenticateApiToken::class])
            ->group(function (): void {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            });

        Route::prefix(config()->string('uat.bubble.prefix'))
            ->middleware([...config()->array('uat.bubble.middleware'), AuthorizeTester::class])
            ->group(function (): void {
                $this->loadRoutesFrom(__DIR__.'/../routes/bubble.php');
            });

        return $this;
    }

    protected function bootCommands(): static
    {
        if (! $this->app->runningInConsole()) {
            return $this; // @codeCoverageIgnore
        }

        $this->commands([
            CreateTokenCommand::class,
            RevokeTokenCommand::class,
        ]);

        return $this;
    }

    protected function bootSkill(): static
    {
        if (! $this->app->runningInConsole()) {
            return $this; // @codeCoverageIgnore
        }

        $this->publishes([
            __DIR__.'/../resources/boost/skills/uat-scenarios' => base_path('.claude/skills/uat-scenarios'),
        ], 'uat-skill');

        return $this;
    }
}
