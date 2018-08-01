<?php

namespace OWC\PDC\Verordeningen\Tests\Config;

use Mockery as m;
use OWC\PDC\Base\Foundation\Loader;
use OWC\PDC\Base\Foundation\Plugin;
use OWC\PDC\Verordeningen\Shortcode\Shortcode;
use OWC\PDC\Verordeningen\Tests\Unit\TestCase;
use OWC\PDC\Base\Foundation\Config;

class TestShortcode extends TestCase
{

	/**
	 * @var Shortcode
	 */
	protected $service;

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

		$this->config = m::mock(Config::class);

		$this->plugin         = m::mock(Plugin::class);
		$this->plugin->config = $this->config;
		$this->plugin->loader = m::mock(Loader::class);

		$this->service = new Shortcode();
	}

	public function tearDown()
	{
		\WP_Mock::tearDown();
	}

	/** @test */
	public function shortcode_is_rendered_incorrectly_when_id_is_not_set()
	{
		\WP_Mock::userFunction('shortcode_atts', [
			'args'   => null,
			'return' => null
		]);

		\WP_Mock::userFunction('get_post_status', [
				'args'   => $this->postID,
				'return' => true
			]
		);

		$attributes = [
			'id' => null
		];

		$actual = $this->service->addShortcode($attributes);

		$this->assertFalse($actual);
	}

	/** @test */
	public function shortcode_is_rendered_incorrectly_when_post_does_not_exist()
	{
		\WP_Mock::passthruFunction('shortcode_atts', [
			'return_arg' => 1
		]);

		\WP_Mock::userFunction('get_post_status', [
				'args'   => $this->postID,
				'return' => false
			]
		);

		$attributes = [
			'id' => $this->postID
		];

		$shortcode = $this->service->addShortcode($attributes);

		$this->assertFalse($shortcode);
	}

	/** @test */
	public function shortcode_is_rendered_correctly()
	{
		\WP_Mock::passthruFunction('shortcode_atts', [
			'return_arg' => 1
		]);

		\WP_Mock::userFunction('get_post_status', [
				'args'   => $this->postID,
				'return' => true
			]
		);

		\WP_Mock::userFunction('get_the_title', [
			'args' => $this->postID,
			'return' => 'test'
		]);

		\WP_Mock::passthruFunction('absint', [
			'return_args' => 1
		]);

		\WP_Mock::userFunction('get_metadata', [
				'args'   => [
					'post',
					$this->postID
				],
				'return' => [
					'_pdc-verordening-active-date' => null,
					'_pdc-verordening-link'       => 'www.yahoo.com',
					'_pdc-verordening-new-link'   => null,
				]
			]
		);

		$attributes = [
			'id' => $this->postID
		];

		$actual   = $this->service->addShortcode($attributes);
		$expected = '<a href="www.yahoo.com" class="pdc-verordening-link" title="test">test</a>';

		$this->assertEquals($actual, $expected);
	}

	/** @test */
	public function shortcode_is_rendered_correctly_when_date_is_not_active()
	{
		\WP_Mock::passthruFunction('shortcode_atts', [
			'return_arg' => 1
		]);

		\WP_Mock::userFunction('get_post_status', [
				'args'   => $this->postID,
				'return' => true
			]
		);

		\WP_Mock::userFunction('get_the_title', [
			'args' => $this->postID,
			'return' => 'test'
		]);

		\WP_Mock::passthruFunction('absint', [
			'return_args' => 1
		]);

		\WP_Mock::userFunction('get_metadata', [
				'args'   => [
					'post',
					$this->postID
				],
				'return' => [
					'_pdc-verordening-link'       => 'www.yahoo.com',
					'_pdc-verordening-new-link'   => 'www.google.com',
					'_pdc-verordening-active-date' => '23-05-3000',
				]
			]
		);

		$attributes = [
			'id' => $this->postID
		];

		$actual   = $this->service->addShortcode($attributes);
		$expected = '<a href="www.yahoo.com" class="pdc-verordening-link" title="test">test</a>';

		$this->assertEquals($actual, $expected);
	}

	/** @test */
	public function shortcode_is_rendered_correctly_when_date_is_active_but_link_is_not()
	{
		\WP_Mock::passthruFunction('shortcode_atts', [
			'return_arg' => 1
		]);

		\WP_Mock::userFunction('get_post_status', [
				'args'   => $this->postID,
				'return' => true
			]
		);

		\WP_Mock::userFunction('get_the_title', [
			'args' => $this->postID,
			'return' => 'test'
		]);

		\WP_Mock::passthruFunction('absint', [
			'return_args' => 1
		]);

		\WP_Mock::userFunction('get_metadata', [
				'args'   => [
					'post',
					$this->postID
				],
				'return' => [
					'key'                   => 'value',
					'_pdc-verordening-link'       => 'www.yahoo.com',
					'_pdc-verordening-new-link'   => null,
					'_pdc-verordening-active-date' => '06-05-2018',
				]
			]
		);

		$attributes = [
			'id' => $this->postID
		];

		$actual   = $this->service->addShortcode($attributes);
		$expected = '<a href="" class="pdc-verordening-link" title="test">test</a>';

		$this->assertEquals($actual, $expected);
	}

	/** @test */
	public function shortcode_is_rendered_correctly_when_date_is_active()
	{
		\WP_Mock::passthruFunction('shortcode_atts', [
			'return_arg' => 1
		]);

		\WP_Mock::userFunction('get_post_status', [
				'args'   => $this->postID,
				'return' => true
			]
		);

		\WP_Mock::userFunction('get_the_title', [
			'args' => $this->postID,
			'return' => 'test'
		]);

		\WP_Mock::passthruFunction('absint', [
			'return_args' => 1
		]);

		\WP_Mock::userFunction('get_metadata', [
				'args'   => [
					'post',
					$this->postID
				],
				'return' => [
					'_pdc-verordening-link'       => 'www.yahoo.com',
					'_pdc-verordening-new-link'   => 'www.google.com',
					'_pdc-verordening-active-date' => '06-05-2018',
				]
			]
		);

		$attributes = [
			'id' => $this->postID
		];

		$actual   = $this->service->addShortcode($attributes);
		$expected = '<a href="www.google.com" class="pdc-verordening-link" title="test">test</a>';

		$this->assertEquals($actual, $expected);
	}
}
