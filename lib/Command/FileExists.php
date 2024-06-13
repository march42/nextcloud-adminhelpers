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
use OCP\Files\Folder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FileExists extends Command {
	public function __construct(
		private FileUtils $fileUtils,
	) {
		parent::__construct();
	}

	protected function configure(): void {
		$this
			->setName('files:exists')
			->setDescription('check if a file exists')
			->addArgument('file', InputArgument::REQUIRED, "Source file id or Nextcloud path")
			->addOption('type', 't', InputOption::VALUE_OPTIONAL, "check for type ignore,file,folder", 'file')
			;
	}

	public function execute(InputInterface $input, OutputInterface $output): int {
		$fileInput = $input->getArgument('file');
		$node = $this->fileUtils->getNode($fileInput);

		if (!$node) {
			$output->writeln("<error>$fileInput not found</error>");
			return self::FAILURE;
		}

		$nodeType = $input->getOption('type');
		if ($nodeType === "file" && !($node instanceof File)) {
			$output->writeln("<error>$fileInput is not a file</error>");
			return self::FAILURE;
		}
		elseif ($nodeType === "folder" && !($node instanceof Folder)) {
			$output->writeln("<error>$fileInput is not a folder</error>");
			return self::FAILURE;
		}
		elseif ($nodeType !== "ignore") {
			$output->writeln("<error>invalid type=$nodeType</error>");
			return self::FAILURE;
		}

		$output->writeln("<info>$fileInput exists</info>");
		return self::SUCCESS;
	}
}
