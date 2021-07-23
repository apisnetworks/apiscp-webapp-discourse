<?php declare(strict_types=1);
/*
 * Copyright (C) Apis Networks, Inc - All Rights Reserved.
 *
 * Unauthorized copying of this file, via any medium, is
 * strictly prohibited without consent. Any dissemination of
 * material herein is prohibited.
 *
 * For licensing inquiries email <licensing@apisnetworks.com>
 *
 * Written by Matt Saladna <matt@apisnetworks.com>, July 2021
 */

namespace Module\Support\Webapps\App\Type\Discourse;

use Opcenter\Net\Port;

/**
 * Discourse Unicorn launcher
 *
 * @package Module\Support\Webapps\App\Type\Discourse
 *
 */
class Launcher
{
	use \ContextableTrait;
	use \apnscpFunctionInterceptorTrait;

	public const LAUNCHER_NAME = 'launch.sh';

	protected string $appRoot;

	protected function __construct(string $approot) {
		$this->appRoot = $approot;
	}

	/**
	 * Create wrapper
	 *
	 * @param int $port
	 * @return bool
	 */
	public function create(int $port): bool
	{
		if (!Port::free($port)) {
			fatal("Port %d/TCP is in use", $port);
		}
		$svc = \Opcenter\SiteConfiguration::shallow($this->getAuthContext());
		$command = (string)(new \Opcenter\Provisioning\ConfigurationWriter('@webapp(discourse)::unicorn-launcher',
			$svc))->compile([
			'svc'     => $svc,
			'afi'     => $this->getApnscpFunctionInterceptor(),
			'port'    => $port,
			'approot' => $this->appRoot
		]);

		$this->file_put_file_contents(
			$this->appRoot . '/' . self::LAUNCHER_NAME,
			$command
		);

		return $this->file_chmod($this->appRoot . '/' . self::LAUNCHER_NAME, 755);
	}

	/**
	 * Launcher exists
	 *
	 * @return bool
	 */
	public function exists(): bool
	{
		return $this->file_exists($this->appRoot . '/' . self::LAUNCHER_NAME);
	}

	/**
	 * Get port from wrapper
	 * @return int
	 */
	public function getPort(): int
	{
		if (!$this->exists()) {
			fatal("Launcher not configured in `%s'", $this->appRoot);
		}

		$command = $this->file_get_file_contents($this->appRoot . '/' . self::LAUNCHER_NAME);
		if (!preg_match('/(?:\s|\b)(?:-p|--port)\s*(\d+)/', $command, $matches)) {
			fatal("Unable to detect report in `%s'", $this->appRoot . '/' . self::LAUNCHER_NAME);
		}

		return (int)$matches[1];
	}

	/**
	 * Run Discourse process
	 *
	 * @return int
	 */
	public function start(): ?int
	{
		$ret = $this->pman_run($this->getCommand());
		if (!$ret || !$ret['success']) {
			return null;
		}
		return $this->getPid();
	}

	/**
	 * Discourse (Unicorn process) running
	 *
	 * @return bool
	 */
	public function running(): bool
	{
		if (null === ($pid = $this->getPid())) {
			return false;
		}

		$processes = $this->pman_stat($pid);
		return $pid > 0 && $processes &&
			$processes['comm'] === 'ruby';
	}

	/**
	 * Get process ID
	 *
	 * @return int|null
	 */
	private function getPid(): ?int
	{
		if (!$this->file_exists($file = $this->getPidFile())) {
			return null;
		}

		$this->pman_flush();
		return (int)$this->file_get_file_contents($file);
	}

	/**
	 * Get PID file
	 *
	 * @return string
	 */
	private function getPidFile(): string
	{
		return $this->appRoot . '/tmp/pids/unicorn.pid';
	}

	/**
	 * Stop launcher
	 *
	 * @return bool
	 */
	public function stop(): bool
	{
		if (null === ($pid = $this->getPid())) {
			return warn("Discourse is not running");
		}

		return $this->pman_signal($pid, SIGTERM);
	}

	/**
	 * Restart Discourse instance
	 *
	 * @return bool
	 */
	public function restart(): bool
	{
		$this->stop();
		return null !== $this->start();
	}

	/**
	 * Get launcher command
	 *
	 * @return string
	 */
	public function getCommand(): string
	{
		return '/bin/bash -ic ' . escapeshellarg($this->appRoot . '/' . self::LAUNCHER_NAME);
	}
}