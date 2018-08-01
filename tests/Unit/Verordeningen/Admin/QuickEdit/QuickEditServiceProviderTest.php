<?php

namespace OWC\PDC\Verordeningen\Tests\Admin\QuickEdit;

use Mockery as m;
use OWC\PDC\Base\Foundation\Config;
use OWC\PDC\Base\Foundation\Plugin;
use OWC\PDC\Base\Foundation\Loader;
use OWC\PDC\Verordeningen\Admin\QuickEdit\QuickEditServiceProvider;
use OWC\PDC\Verordeningen\Tests\Unit\TestCase;
use StdClass;
use WP_Mock;

class QuickEditServiceProviderTest extends TestCase
{
	/**
	 * @var
	 */
	protected $service;

	/**
	 * @var
	 */
	protected $plugin;

	/**
	 * @var array
	 */
	protected $stub;

	/**
	 * @var
	 */
	protected $post;

	public function setUp()
	{
		WP_Mock::setUp();

		$this->setRunTestInSeparateProcess(true);
		$this->setRunClassInSeparateProcess(true);

		$config       = m::mock(Config::class);
		$this->plugin = m::mock(Plugin::class);

		$this->plugin->config = $config;
		$this->plugin->loader = m::mock(Loader::class);

		$this->stub = [
			'new-link'   => [
				'metaboxKey' => 'new-link',
				'label'      => 'New link'
			],
			'link'       => [
				'metaboxKey' => 'link',
				'label'      => 'Link'
			],
			'active-date' => [
				'metaboxKey' => 'active-date',
				'label'      => 'Date new verordening active'
			]
		];

		$this->post            = new StdClass();
		$this->post->ID        = 5;
		$this->post->post_type = 'page';

		$this->service = new QuickEditServiceProvider($this->plugin);
	}

	public function tearDown()
	{
		WP_Mock::tearDown();
	}

	/** @test */
	public function it_registers_hook_correctly()
	{
		$this->plugin->loader->shouldReceive('addAction')->withArgs([
			'quick_edit_custom_box',
			$this->service,
			'registerQuickEditHandler',
			10,
			2
		])->once();

		$this->plugin->loader->shouldReceive('addAction')->withArgs([
			'save_post',
			$this->service,
			'registerSavePostHandler',
			10,
			2
		])->once();

		$this->plugin->loader->shouldReceive('addAction')->withArgs([
			'admin_footer',
			$this->service,
			'renderFooterScript',
			10,
			1
		])->once();

		$this->plugin->loader->shouldReceive('addFilter')->withArgs([
			'post_row_actions',
			$this->service,
			'addRowActions',
			10,
			2
		])->once();

		$this->service->register();

		$this->assertTrue(true);
	}

	/** @test */
	public function it_adds_actions_to_rows_if_all_metadata_is_filled_in()
	{

		$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" aria-label="&#8220;Verordening 1&#8221; snel bewerken">Snel&nbsp;bewerken</a>';

		WP_Mock::userFunction('get_post_meta', [
			'times'           => 3,
			'return_in_order' => [
				'www.google.com',
				'www.yahoo.com',
				'23-05-2018'
			]
		]);

		$this->service->setQuickEditHandlers();
		$actual                           = $this->service->addRowActions($actions, $this->post);
		$expected['inline hide-if-no-js'] = '<a href="#" data-new-link="www.google.com" data-link="www.yahoo.com" data-active-date="23-05-2018" class="editinline" aria-label="&#8220;Verordening 1&#8221; snel bewerken">Snel&nbsp;bewerken</a>';

		$this->assertEquals($expected, $actual);
	}

	/** @test */
	public function it_adds_actions_to_rows_if_not_all_metadata_is_filled_in()
	{

		$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" aria-label="&#8220;Verordening 1&#8221; snel bewerken">Snel&nbsp;bewerken</a>';

		WP_Mock::userFunction('get_post_meta', [
			'times'           => 3,
			'return_in_order' => [
				null,
				'www.yahoo.com',
				'23-05-2018'
			]
		]);

		$this->service->setQuickEditHandlers();
		$actual                           = $this->service->addRowActions($actions, $this->post);
		$expected['inline hide-if-no-js'] = '<a href="#" data-link="www.yahoo.com" data-active-date="23-05-2018" class="editinline" aria-label="&#8220;Verordening 1&#8221; snel bewerken">Snel&nbsp;bewerken</a>';

		$this->assertEquals($expected, $actual);
	}

	/** @test */
	public function if_returns_null_if_post_is_revision()
	{

		WP_Mock::userFunction('wp_is_post_revision', [
			'return' => true
		]);

		$actual = $this->service->registerSavePostHandler($this->post->ID, $this->post);
		$this->assertNull($actual);
	}

	/** @test */
	public function if_returns_null_if_post_is_autosave()
	{

		WP_Mock::userFunction('wp_is_post_autosave', [
			'return' => true
		]);

		$actual = $this->service->registerSavePostHandler($this->post->ID, $this->post);
		$this->assertNull($actual);
	}

	/** @test */
	public function if_returns_null_if_post_type_is_not_correctly()
	{

		$actual = $this->service->registerSavePostHandler($this->post->ID, $this->post);

		$this->assertNull($actual);
	}

	/** @test */
	public function if_returns_null_if_user_cannot_edit_post()
	{

		$this->post->post_type = 'pdc-verordeningen';

		WP_Mock::userFunction('current_user_can', [
			'return' => false
		]);

		$actual = $this->service->registerSavePostHandler($this->post->ID, $this->post);

		$this->assertNull($actual);
	}

	/** @test */
	public function if_returns_null_if_all_the_checks_pass()
	{

		$this->post->post_type = 'pdc-verordeningen';

		WP_Mock::userFunction('current_user_can', [
			'return' => true
		]);

		$this->service->setQuickEditHandlers();

		$_POST['_pdc-verordening-link'] = 'www.yahoo.com';

		WP_Mock::userFunction('update_post_meta', [
			'times'  => 1,
			'return' => true
		]);

		$actual = $this->service->registerSavePostHandler($this->post->ID, $this->post);

		$this->assertNull($actual);
	}
}
