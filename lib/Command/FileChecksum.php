<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2024 Marc Hefter <marchefter@march42.net>
 *
 * @author Marc Hefter <marchefter@march42.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace OCA\AdminHelpers\Command;

use OC\Core\Command\Info\FileUtils;
use OCP\Files\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FileChecksum extends Command {
	public function __construct(
		private FileUtils $fileUtils,
	) {
		parent::__construct();
	}

	protected function configure(): void {
		$this
			->setName('files:checksum')
			->setDescription('get stored checksum of file')
			->addArgument('file', InputArgument::REQUIRED, "Source file id or Nextcloud path")
			->addOption('type', 't', InputOption::VALUE_OPTIONAL, "hash type sha512,sha256,md5 whatever your PHP supports (check hash_algos())", 'sha512')
			;
	}

	public function execute(InputInterface $input, OutputInterface $output): int {
		$fileInput = $input->getArgument('file');
		$node = $this->fileUtils->getNode($fileInput);
		$hashType = $input->getOption('type');

		if ( ! $hashType || "?" === $hashType ) {
			$output->writeln("<error>listing available hash algorithms</error>");
			foreach (hash_algos() as $key => $value) {
				$output->writeln("<info>$key => $value</info>");
			}
			return self::FAILURE;
		}

		if (!$node) {
			$output->writeln("<error>$fileInput not found</error>");
			return self::FAILURE;
		}

		if (!($node instanceof File)) {
			$output->writeln("<error>$fileInput is not a file</error>");
			return self::FAILURE;
		}

		$checksum = $node->hash($hashType);
		$output->writeln("$checksum");
		return self::SUCCESS;
	}
}
