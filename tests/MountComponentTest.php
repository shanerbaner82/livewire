<?php

namespace Tests;

use Livewire\Component;
use Livewire\LivewireManager;

class MountComponentTest extends TestCase
{
    /** @test */
    public function it_resolves_the_mount_parameters()
    {
        $component = app(LivewireManager::class)->test(ComponentWithOptionalParameters::class);
        $this->assertSame(null, $component->foo);
        $this->assertSame([], $component->bar);

        $component = app(LivewireManager::class)->test(ComponentWithOptionalParameters::class, ['foo' => 'caleb']);
        $this->assertSame('caleb', $component->foo);
        $this->assertSame([], $component->bar);

        $component = app(LivewireManager::class)->test(ComponentWithOptionalParameters::class, ['bar' => 'porzio']);
        $this->assertSame(null, $component->foo);
        $this->assertSame('porzio', $component->bar);

        $component = app(LivewireManager::class)->test(ComponentWithOptionalParameters::class, ['foo' => 'caleb', 'bar' => 'porzio']);
        $this->assertSame('caleb', $component->foo);
        $this->assertSame('porzio', $component->bar);

        $component = app(LivewireManager::class)->test(ComponentWithOptionalParameters::class, ['foo' => null, 'bar' => null]);
        $this->assertSame(null, $component->foo);
        $this->assertSame(null, $component->bar);
    }

    /** @test */
    public function mount_parameters_can_be_dependancy_injected()
    {
        $component = app(LivewireManager::class)->test(ComponentWithInjectedParameters::class, [
            'bar' => 'baz',
        ]);
        $this->assertSame(true, $component->foo);
        $this->assertSame('baz', $component->bar);
    }

    /** @test */
    public function mount_parameters_without_typehints_shouldnt_be_resolved_from_the_container()
    {
        $component = app(LivewireManager::class)->test(ComponentWithParameterWithNameCollidingWithLaravelContainer::class, [
            'date' => 'foo',
        ]);
        $this->assertSame('foo', $component->date);
    }
}

class ComponentWithOptionalParameters extends Component
{
    public $foo;
    public $bar;

    public function mount($foo = null, $bar = [])
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function render()
    {
        return view('null-view');
    }
}

class ClassToBeInjected {}

class ComponentWithInjectedParameters extends Component
{
    public $foo;
    public $bar;

    public function mount(ClassToBeInjected $foo, $bar)
    {
        $this->foo = get_class($foo) === ClassToBeInjected::class;
        $this->bar = $bar;
    }

    public function render()
    {
        return view('null-view');
    }
}

class ComponentWithParameterWithNameCollidingWithLaravelContainer extends Component
{
    public $date;

    public function mount($date)
    {
        $this->date = $date;
    }

    public function render()
    {
        return view('null-view');
    }
}
