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


	namespace Module\Support\Webapps\App\Type\Discourse\Reconfiguration;

	use Module\Support\Webapps\App\Type\Discourse\Launcher;
	use Module\Support\Webapps\App\Type\Passenger\Reconfiguration\Restart as RestartParent;
	use Module\Support\Webapps\Traits\WebappUtilities;

	class Restart extends RestartParent
	{
		use WebappUtilities;

		public function handle(&$val): bool
		{
			$launcher = $this->app->getAppRoot() . '/' . Launcher::LAUNCHER_NAME;
			if (!$this->file_exists($launcher)) {
				return Passenger::instantiateContexted(
						$this->getAuthContext(), [$this->app->getAppRoot(), 'ruby'])->restart() &&
					info('Restart may take up to 2 minutes to complete');
			}

			$appRoot = $this->app->getAppRoot();
			$ctx = null;
			$this->getApnscpFunctionInterceptorFromDocroot($appRoot, $ctx);
			return Launcher::instantiateContexted($ctx, [$appRoot])->restart();
		}

		public function getValue()
		{
			return false;
		}
	}