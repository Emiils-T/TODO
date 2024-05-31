<?php

require_once './vendor/autoload.php';

use App\TODO;
use Carbon\Carbon;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
class Machine
{
    private array $list;

    public function __construct()
    {
        $this->list = $this->getList();
    }

    public function addTODO(TODO $todo): void
    {
        $this->list[] = $todo;
        $this->saveListToFile();
    }

    private function saveListToFile(): void
    {
        $filePath = __DIR__ . "/data/data.json";
        $jsonData = json_encode($this->list, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $jsonData);
    }

    public function removeTODOAtIndex(int $index): void
    {
        if (isset($this->list[$index])) {
            unset($this->list[$index]);
            $this->list = array_values($this->list); // Reindex the array
            $this->saveListToFile();
        }
    }

    public function markTODOAsDone(int $index): void
    {
        if (isset($this->list[$index])) {
            $this->list[$index]->setIsDone('yes');
            $this->saveListToFile();
        }
    }

    public function getList(): array
    {
        $filePath = __DIR__ . "/data/data.json";

        if (!file_exists($filePath)) {
            return [];
        }

        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true); // Decode as associative array

        $list = [];
        foreach ($data as $item) {
            $list[] = new TODO(
                $item['name'],
                $item['time'],
                $item['isDone']
            );
        }
        return $list;
    }

    public function getListAsTableRows(): array
    {
        $rows = [];
        foreach ($this->list as $index => $item) {
            $taskCell = $item->getName();
            $timeCell = $item->getTime();
            $doneCell = $item->getIsDone();

            // Apply style if the task is done
            if ($item->getIsDone() === 'yes') {
                $rows[] = [
                    new TableCell($index, ['style' => new TableCellStyle(['fg' => 'green'])]),
                    new TableCell($taskCell, ['style' => new TableCellStyle(['fg' => 'green'])]),
                    new TableCell($timeCell, ['style' => new TableCellStyle(['fg' => 'green'])]),
                    new TableCell($doneCell, ['style' => new TableCellStyle(['fg' => 'green'])]),
                ];
            } else {
                $rows[] = [$index, $taskCell, $timeCell, $doneCell];
            }
        }
        return $rows;
    }
}

$keepWorking = true;
while ($keepWorking) {
    echo "1. Create new TODO.\n2. Show TODO list.\n3. Mark done\n4. Delete TODO\n5. Quit\n";
    $choice = (int) readline("Enter index to select choice: ");

    $machine = new Machine();

    switch ($choice) {
        case 1:
            $time = Carbon::now();
            $todo = readline("Enter Thing to DO:");
            $isDone = "no";
            $todoList = new TODO($todo, $time, $isDone);

            $machine->addTODO($todoList);
            break;
        case 2:
            $output = new ConsoleOutput();
            $table = new Table($output);

            $table
                ->setHeaders(['Index', 'Task Name', 'Date', 'Done?'])
                ->setRows($machine->getListAsTableRows());

            $table->render();
            break;
        case 3:
            $index = (int) readline("Enter the index of the TODO to mark as done: ");
            $machine->markTODOAsDone($index);
            break;
        case 4:
            $index = (int) readline("Enter the index of the TODO to delete: ");
            $machine->removeTODOAtIndex($index);
            break;
        case 5:
            $keepWorking = false;
            break;
        default:
            echo "Invalid choice. Please try again.\n";
    }
}