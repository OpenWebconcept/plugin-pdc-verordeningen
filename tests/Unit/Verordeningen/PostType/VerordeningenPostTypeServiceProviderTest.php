<?php

namespace OWC\PDC\Verordeningen\Tests\PostType;

use Extended_CPT;
use Mockery as m;
use OWC\PDC\Base\Foundation\Config;
use OWC\PDC\Base\Foundation\Loader;
use OWC\PDC\Base\Foundation\Plugin;
use OWC\PDC\Verordeningen\PostType\VerordeningenPostTypeServiceProvider;
use OWC\PDC\Verordeningen\Tests\Unit\TestCase;
use WP_Mock;

class VerordeningenPostTypeServiceProviderTest extends TestCase
{

    /**
     * @var
     */
    protected $service;

    /**
     * @var
     */
    protected $plugin;

    public function setUp()
    {
        WP_Mock::setUp();

        $config       = m::mock(Config::class);
        $this->plugin = m::mock(Plugin::class);

        $this->plugin->config = $config;
        $this->plugin->loader = m::mock(Loader::class);

        $this->service = new VerordeningenPostTypeServiceProvider($this->plugin);
    }

    public function tearDown()
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function check_registration_of_posttype()
    {
        $this->plugin->loader->shouldReceive('addAction')->withArgs([
            'init',
            $this->service,
            'registerPostType'
        ])->once();

        $register = $this->service->register();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_throws_an_exception_if_function_does_exist()
    {

        WP_Mock::userFunction('register_extended_post_type', [
            'times'  => 1,
            'return' => Extended_CPT::class
        ]);

        $actual   = $this->service->registerPostType();
        $expected = Extended_CPT::class;

        $this->assertEquals($expected, $actual);
    }
}
