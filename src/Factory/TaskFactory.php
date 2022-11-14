<?php

namespace App\Factory;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Task>
 *
 * @method static Task|Proxy createOne(array $attributes = [])
 * @method static Task[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Task|Proxy find(object|array|mixed $criteria)
 * @method static Task|Proxy findOrCreate(array $attributes)
 * @method static Task|Proxy first(string $sortedField = 'id')
 * @method static Task|Proxy last(string $sortedField = 'id')
 * @method static Task|Proxy random(array $attributes = [])
 * @method static Task|Proxy randomOrCreate(array $attributes = [])
 * @method static Task[]|Proxy[] all()
 * @method static Task[]|Proxy[] findBy(array $attributes)
 * @method static Task[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Task[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TaskRepository|RepositoryProxy repository()
 * @method Task|Proxy create(array|callable $attributes = [])
 */
final class TaskFactory extends ModelFactory
{
    public function __construct(private SluggerInterface $slugger)
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->sentence(),
            'content' => self::faker()->text(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function(Task $task) {
                $task->setSlug(strtolower($this->slugger->slug($task->getTitle())));
            })
            ;
    }

    protected static function getClass(): string
    {
        return Task::class;
    }
}