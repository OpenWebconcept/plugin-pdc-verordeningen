<?php

namespace OWC\PDC\Verordeningen\Tests\Shortcode;

use Mockery as m;
use OWC\PDC\Base\Foundation\Config;
use OWC\PDC\Base\Foundation\Plugin;
use OWC\PDC\Base\Foundation\Loader;
use OWC\PDC\Verordeningen\Shortcode\Shortcode;
use OWC\PDC\Verordeningen\Shortcode\ShortcodeServiceProvider;
use OWC\PDC\Verordeningen\Tests\Unit\TestCase;

class ShortcodeServiceProviderTest extends TestCase
{

    /**
     * @var ShortcodeServiceProvider
     */
    protected $service;

    /**
     * Shortcode object.
     * @var \OWC\PDC\Verordeningen\Shortcode\Shortcode
     */
    private $shortcode;

    /**
     * Shortcode tag.
     * @var string
     */
    private $tag = 'pdc::verordeningen';

    /**
     * @var
     */
    protected $config;

    /**
     * @var
     */
    protected $plugin;

    /**
     * @var int
     */
    protected $postID = 10;

    public function setUp()
    {
        \WP_Mock::setUp();

        $this->config         = m::mock(Config::class);
        $this->plugin         = m::mock(Plugin::class);
        $this->plugin->config = $this->config;
        $this->plugin->loader = m::mock(Loader::class);

        $this->service = new ShortcodeServiceProvider($this->plugin);

        $this->shortcode = new Shortcode();
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();

        $this->shortcode = null;
    }

    /** @test */
    public function it_registers_the_shortcode_correctly()
    {

        \WP_Mock::userFunction('add_shortcode', [
            'times' => 1,
            'args'  => [
                $this->tag,
                [
                    $this->shortcode,
                    'addShortcode'
                ]
            ],
        ]);

        $this->service->register();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_generate_the_code_correctly()
    {
        $expected = sprintf('<code>[%s id="%d"]</code>', 'pdc::verordeningen', 5);

        $actual = $this->service::generateShortcode(5);

        $this->assertEquals($expected, $actual);
    }
}
