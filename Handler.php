<?php
	/**
 * Copyright (C) Apis Networks, Inc - All Rights Reserved.
 *
 * Unauthorized copying of this file, via any medium, is
 * strictly prohibited without consent. Any dissemination of
 * material herein is prohibited.
 *
 * For licensing inquiries email <licensing@apisnetworks.com>
 *
 * Written by Matt Saladna <matt@apisnetworks.com>, August 2020
 */

	namespace Module\Support\Webapps\App\Type\Discourse;

	use Module\Support\Webapps\App\Type\Passenger\Handler as Passenger;
	use Opcenter\Versioning;

	class Handler extends Passenger
	{
		const NAME = 'Discourse';
		const ADMIN_PATH = '/admin';
		const LINK = 'https://discourse.org/';
		const FEAT_ALLOW_SSL = true;
		const FEAT_RECOVERY = false;
		const FEAT_GIT = false;
		const MAXMIND_KEY_PREF = 'auth.geolite2';

		public function changePassword(string $password): bool
		{
			return $this->discourse_change_admin($this->hostname, $this->path, array('password' => $password));
		}

		public function getInstallableVersions(): array
		{
			$hasRedis6 = version_compare($this->redis_version(), '6.2', '>=');
			return array_filter(
				$this->discourse_get_versions(),
				static fn ($version) => Versioning::asMajor($version) < 3 || $hasRedis6
			);
		}
	}