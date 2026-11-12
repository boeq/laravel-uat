<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests;

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
use Boeq\Uat\Contracts\Access\ResolvesTester;
use Boeq\Uat\Contracts\Feedback\ResolvesFeedback;
use Boeq\Uat\Contracts\Scenarios\ChangesScenarioStatus;
use Boeq\Uat\Contracts\Scenarios\ListsScenariosForTester;
use Boeq\Uat\Contracts\Scenarios\SavesScenario;
use Boeq\Uat\Contracts\Scenarios\SavesTranslation;
use Boeq\Uat\Contracts\Sessions\AcceptsSession;
use Boeq\Uat\Contracts\Sessions\CompletesSession;
use Boeq\Uat\Contracts\Sessions\JudgesStep;
use Boeq\Uat\Contracts\Sessions\StartsSession;
use Boeq\Uat\Contracts\Tokens\CreatesApiToken;
use Boeq\Uat\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_merges_the_config(): void
    {
        $this->assertSame('uat_', config()->string('uat.table_prefix'));
    }

    /**
     * @param  class-string  $contract
     * @param  class-string  $action
     */
    #[Test]
    #[DataProvider('actions')]
    public function it_binds_the_actions(string $contract, string $action): void
    {
        $this->assertInstanceOf($action, app($contract));
    }

    public static function actions(): Iterator
    {
        yield [ResolvesTester::class, ResolveTester::class];
        yield [SavesScenario::class, SaveScenario::class];
        yield [SavesTranslation::class, SaveTranslation::class];
        yield [ChangesScenarioStatus::class, ChangeScenarioStatus::class];
        yield [ListsScenariosForTester::class, ListScenariosForTester::class];
        yield [StartsSession::class, StartSession::class];
        yield [JudgesStep::class, JudgeStep::class];
        yield [AcceptsSession::class, AcceptSession::class];
        yield [CompletesSession::class, CompleteSession::class];
        yield [ResolvesFeedback::class, ResolveFeedback::class];
        yield [CreatesApiToken::class, CreateApiToken::class];
    }

    #[Test]
    #[DataProvider('tags')]
    public function it_publishes_its_files(string $tag): void
    {
        $this->assertNotEmpty(ServiceProvider::pathsToPublish(ServiceProvider::class, $tag));
    }

    public static function tags(): Iterator
    {
        yield ['config'];
        yield ['lang'];
        yield ['views'];
        yield ['uat-skill'];
    }

    #[Test]
    public function it_registers_the_commands(): void
    {
        $this->assertArrayHasKey('uat:create-token', Artisan::all());
        $this->assertArrayHasKey('uat:revoke-token', Artisan::all());
    }
}
