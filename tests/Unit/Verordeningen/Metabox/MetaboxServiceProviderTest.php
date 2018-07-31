<?php

namespace OWC\PDC\Verordeningen\Tests\Metabox;

use Mockery as m;
use OWC\PDC\Base\Foundation\Config;
use OWC\PDC\Base\Foundation\Loader;
use OWC\PDC\Base\Foundation\Plugin;
use OWC\PDC\Verordeningen\Metabox\MetaboxServiceProvider;
use OWC\PDC\Verordeningen\Tests\Unit\TestCase;
use WP_Mock;

class MetaboxServiceProviderTest extends TestCase
{

	public function setUp()
	{
		WP_Mock::setUp();
	}

	public function tearDown()
	{
		WP_Mock::tearDown();
	}

	/** @test */
	public function check_registration_of_metaboxes()
	{
		$config = m::mock(Config::class);
		$plugin = m::mock(Plugin::class);

		$plugin->config = $config;
		$plugin->loader = m::mock(Loader::class);

		$service = new MetaboxServiceProvider($plugin);

		$plugin->loader->shouldReceive('addFilter')->withArgs([
			'rwmb_meta_boxes',
			$service,
			'registerMetaboxes',
			10,
			1
		])->once();

		$service->register();

		$prefix = '_pdc-verordening';

		$expected = [
			[
				'id'         => 'pdc-verordeningen',
				'title'      => __('Verordening settings', 'pdc-verordeningen'),
				'post_types' => ['pdc-verordeningen'],
				'context'    => 'normal',
				'priority'   => 'high',
				'autosave'   => true,
				'fields'     => [
					[
						'id'   => "{$prefix}-price",
						'name' => __('Verordening price', 'pdc-verordeningen'),
						'desc' => __('Price in &euro;', 'pdc-verordeningen'),
						'type' => 'text',
					],
					[
						'id'   => "{$prefix}-new-price",
						'name' => __('Verordening new price', 'pdc-verordeningen'),
						'desc' => __('Price in &euro;', 'pdc-verordeningen'),
						'type' => 'text',
					],
					[
						'id'         => "{$prefix}-active-date",
						'name'       => esc_html__('Date new verordening active', 'pdc-verordeningen'),
						'type'       => 'date',
						'js_options' => [
							'dateFormat'      => esc_html__('dd-mm-yy', 'pdc-verordeningen'),
							'altFormat'       => 'yy-mm-dd',
							'changeMonth'     => true,
							'changeYear'      => true,
							'showButtonPanel' => true,
							'minDate'         => 0
						],
						'desc'       => esc_html__('(dd-mm-yy)', 'pdc-verordeningen'),
					]
				]
			],
			[
				'id'    => 'pdc-verordeningen',
				'title' => __('Verordening settings', 'pdc-verordeningen')
			]
		];

		$actual = [
			'id'    => 'pdc-verordeningen',
			'title' => __('Verordening settings', 'pdc-verordeningen')
		];

		$this->assertContains($actual, $service->registerMetaboxes($expected));
	}
}
