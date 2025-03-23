<?php
// Archivo de tareas
define('TASK_FILE', 'Task-Fenix36.json');
define('ARCHIVE_FILE', 'ash.txt');

function loadTasks() {
    if (!file_exists(TASK_FILE)) {
        return [];
    }
    $json = file_get_contents(TASK_FILE);
    $tasks = json_decode($json, true) ?: [];

    // Asegurar que las tareas sean un array asociativo con padres
    if (!is_array($tasks) || array_values($tasks) === $tasks) {
        return [];
    }
    return $tasks;
}

function saveTasks($tasks) {
    file_put_contents(TASK_FILE, json_encode($tasks));
}

function saveCompletedTasksToFile($tasks, $filename) {
    $completedTasks = [];
    foreach ($tasks as $parent => $taskList) {
        foreach ($taskList as $task) {
            if ($task['done']) {
                $completedTasks[] = "- " . $task['name'];
            }
        }
    }
    if (!empty($completedTasks)) {
        $date = date('Y-m-d H:i:s');
        $fileContent = "Tareas completadas (eliminadas el $date):\n" . implode("\n", $completedTasks) . "\n";
        file_put_contents($filename, $fileContent, FILE_APPEND);
    }
}

function showTasks($tasks) {
    echo "Lista de tareas:\n";
    if (empty($tasks)) {
        echo "No hay tareas.\n";
    } else {
        foreach ($tasks as $parent => $taskList) {
            echo "\n";
            echo "=====================================================\n";
            echo "[🐦🔥 $parent]:\n";
            echo "\n";
            foreach ($taskList as $index => $task) {
                $status = $task['done'] ? "[x]" : "[ ]";
                echo "$status " . ($index + 1) . ". " . $task['name'] . "\n";
            }
        }
    }
}

$tasks = loadTasks();

while (true) {

    echo "\nBienvenido al gestor de tareas!\n";
    showTasks($tasks);
    echo "\n";
    echo "___________           .__          _____ ____   \n";
    echo "\_   _____/___   ____ |__|__  ___ /  |  /_   |  \n";
    echo " |    __)/ __ \ /    \|  \  \/  //   |  ||   |  \n";
    echo " |     \|  ___/|   |  \  |>    </    ^   /   |  \n";
    echo " \___  /\____  >___|  /__/__/\_ \____   ||___|  \n";
    echo "     \/      \/     \/         \/    |__|       \n";
    echo "\n";
    // Menú de opciones
    echo "1. Agregar una tarea\n";
    echo "2. Crear un nuevo padre\n";
    echo "\033[31m3. Borrar todas las tareas          ~ De manera irrecuperable. \033[0m\n";
    echo "\033[31m4. Borrar un padre                  ~ De manera irrecuperable. \033[0m\n";
    echo "\033[31m5. Borrar una tarea específica      ~ De manera irrecuperable. \033[0m\n";
    echo "\033[31m6. Borrar tareas completadas [x]    ~ Guarda las tareas antiguas si es necesario. \033[0m\n";
    echo "\033[32m7. Marcar una tarea como realizada\033[0m\n";
    echo "\033[33m8. Quitar tarea como realizada    \033[0m\n";
    echo "9. Ver tareas\n";
    echo "0. Salir\n";
    echo "\n";
    echo "\033[30;43m🌱 Menos papel, más conciencia ecológica.\033[0m \n";
    echo "Selecciona una opción: ";
    $option = intval(trim(fgets(STDIN)));

    switch ($option) {
        case 1:
            if (empty($tasks)) {
                echo "No hay padres disponibles. Crea uno primero.\n";
                break;
            }
            echo "Selecciona el padre al que deseas asignar la tarea:\n";
            $parents = array_keys($tasks);
            foreach ($parents as $index => $parent) {
                echo ($index + 1) . ". $parent\n";
            }
            echo "Número de padre: ";
            $parentIndex = intval(trim(fgets(STDIN))) - 1;
            if (!isset($parents[$parentIndex])) {
                echo "Selección no válida.\n";
                break;
            }
            $parent = $parents[$parentIndex];

            echo "Escribe el nombre de la nueva tarea: ";
            $taskName = trim(fgets(STDIN));
            $tasks[$parent][] = ['name' => $taskName, 'done' => false];
            saveTasks($tasks);
            echo "Tarea agregada con éxito.\n";
            break;

        case 2:
            echo "Ingresa el nombre del nuevo padre: ";
            $parentName = trim(fgets(STDIN));
            if (!isset($tasks[$parentName])) {
                $tasks[$parentName] = [];
                saveTasks($tasks);
                echo "Padre '$parentName' creado con éxito.\n";
            } else {
                echo "Ese padre ya existe.\n";
            }
            break;

        case 3:
            $tasks = [];
            saveTasks($tasks);
            echo "Todas las tareas han sido eliminadas.\n";
            break;

        case 4:
            if (empty($tasks)) {
                echo "No hay padres disponibles para borrar.\n";
                break;
            }
            echo "Selecciona el padre a borrar:\n";
            $parents = array_keys($tasks);
            foreach ($parents as $index => $parent) {
                echo ($index + 1) . ". $parent\n";
            }
            echo "Número de padre: ";
            $parentIndex = intval(trim(fgets(STDIN))) - 1;
            if (!isset($parents[$parentIndex])) {
                echo "Selección no válida.\n";
                break;
            }
            $parent = $parents[$parentIndex];

            echo "¿Estás seguro de que quieres borrar el padre '$parent' y todas sus tareas? (s/n): ";
            $confirm = trim(fgets(STDIN));
            if (strtolower($confirm) === 's') {
                unset($tasks[$parent]);
                saveTasks($tasks);
                echo "Padre y sus tareas eliminados.\n";
            } else {
                echo "Operación cancelada.\n";
            }
            break;

        case 5:
            if (empty($tasks)) {
                echo "No hay tareas para borrar.\n";
                break;
            }
            echo "Selecciona el padre de la tarea a borrar:\n";
            $parents = array_keys($tasks);
            foreach ($parents as $index => $parent) {
                echo ($index + 1) . ". $parent\n";
            }
            echo "Número de padre: ";
            $parentIndex = intval(trim(fgets(STDIN))) - 1;
            if (!isset($parents[$parentIndex])) {
                echo "Selección no válida.\n";
                break;
            }
            $parent = $parents[$parentIndex];

            if (empty($tasks[$parent])) {
                echo "No hay tareas en este padre.\n";
                break;
            }

            echo "Selecciona la tarea a borrar:\n";
            foreach ($tasks[$parent] as $index => $task) {
                echo ($index + 1) . ". " . $task['name'] . "\n";
            }
            echo "Número de tarea: ";
            $taskIndex = intval(trim(fgets(STDIN))) - 1;
            if (!isset($tasks[$parent][$taskIndex])) {
                echo "Selección no válida.\n";
                break;
            }
            array_splice($tasks[$parent], $taskIndex, 1);
            saveTasks($tasks);
            echo "Tarea eliminada con éxito.\n";
            break;

        case 6:
            echo "¿Quieres guardar las tareas completadas antes de borrarlas? (s/n): ";
            $saveOption = trim(fgets(STDIN));
            if (strtolower($saveOption) === 's') {
                saveCompletedTasksToFile($tasks, ARCHIVE_FILE);
                echo "Tareas completadas guardadas en 'ash.txt'.\n";
            }
            foreach ($tasks as $parent => &$taskList) {
                $taskList = array_filter($taskList, fn($task) => !$task['done']);
            }
            saveTasks($tasks);
            echo "Tareas completadas eliminadas.\n";
            break;

        case 7:
            if (empty($tasks)) {
                echo "No hay tareas para marcar como realizadas.\n";
                break;
            }
            echo "Selecciona el padre de la tarea a marcar como realizada:\n";
            $parents = array_keys($tasks);
            foreach ($parents as $index => $parent) {
                echo ($index + 1) . ". $parent\n";
            }
            echo "Número de padre: ";
            $parentIndex = intval(trim(fgets(STDIN))) - 1;
            if (!isset($parents[$parentIndex])) {
                echo "Selección no válida.\n";
                break;
            }
            $parent = $parents[$parentIndex];

            if (empty($tasks[$parent])) {
                echo "No hay tareas en este padre.\n";
                break;
            }

            echo "Selecciona la tarea a marcar como realizada:\n";
            foreach ($tasks[$parent] as $index => $task) {
                echo ($index + 1) . ". " . $task['name'] . "\n";
            }
            echo "Número de tarea: ";
            $taskIndex = intval(trim(fgets(STDIN))) - 1;
            if (!isset($tasks[$parent][$taskIndex])) {
                echo "Selección no válida.\n";
                break;
            }
            $tasks[$parent][$taskIndex]['done'] = true;
            saveTasks($tasks);
            echo "Tarea marcada como realizada.\n";
            break;

        case 8:
            if (empty($tasks)) {
                echo "No hay tareas para desmarcar.\n";
                break;
            }
            echo "Selecciona el padre de la tarea a desmarcar:\n";
            $parents = array_keys($tasks);
            foreach ($parents as $index => $parent) {
                echo ($index + 1) . ". $parent\n";
            }
            echo "Número de padre: ";
            $parentIndex = intval(trim(fgets(STDIN))) - 1;
            if (!isset($parents[$parentIndex])) {
                echo "Selección no válida.\n";
                break;
            }
            $parent = $parents[$parentIndex];

            if (empty($tasks[$parent])) {
                echo "No hay tareas en este padre.\n";
                break;
            }

            echo "Selecciona la tarea a desmarcar:\n";
            foreach ($tasks[$parent] as $index => $task) {
                echo ($index + 1) . ". " . $task['name'] . "\n";
            }
            echo "Número de tarea: ";
            $taskIndex = intval(trim(fgets(STDIN))) - 1;
            if (!isset($tasks[$parent][$taskIndex])) {
                echo "Selección no válida.\n";
                break;
            }
            $tasks[$parent][$taskIndex]['done'] = false;
            saveTasks($tasks);
            echo "Tarea desmarcada.\n";
            break;

        case 9:
            // Ver tareas

            break;

        case 0:
            echo "Saliendo del gestor de tareas...\n";
            exit;

        default:
            echo "Opción no válida. Inténtalo de nuevo.\n";
            break;
    }
}
