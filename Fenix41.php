<?php
// Archivo de tareas
define('TASK_FILE', 'Task-Fenix36.json');
define('ARCHIVE_FILE', 'ash.txt');

echo "\n";
echo "\n";
echo "  +---------------------------------------------------------+\n";
echo "  | Fenix36::                                               |\n";
echo "  | > Metodología KANBAN para trabajar de manera individual |\n";
echo "  +---------------------------------------------------------+\n";

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
    echo "1.  Agregar una tarea \n";
    echo "2.  Crear un nuevo padre \n";
    echo "\033[31m3.  Borrar todas las tareas          ~ De manera irrecuperable. \033[0m\n";
    echo "\033[31m4.  Borrar un padre                  ~ De manera irrecuperable. \033[0m\n";
    echo "\033[31m5.  Borrar una tarea específica      ~ De manera irrecuperable. \033[0m\n";
    echo "\033[31m6.  Borrar tareas completadas [x]    ~ Guarda las tareas antiguas si es necesario. \033[0m\n";
    echo "\033[32m7.  Marcar una tarea como realizada \033[0m\n";
    echo "\033[33m8.  Quitar tarea como realizada \033[0m\n";
    echo "\033[33m9.  Renombrar un padre o una tarea \033[0m\n";
    echo "\033[33m10. Mover posición de una tarea \033[0m\n";
    echo "11. Ver tareas \n";
    echo "0. Salir \n";
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
            echo "¿Qué deseas renombrar?\n";
            echo "1. Una tarea\n";
            echo "2. Un padre\n";
            echo "Selecciona una opción: ";
            $renameOption = intval(trim(fgets(STDIN)));

            if ($renameOption === 1) {
                // Renombrar una tarea
                if (empty($tasks)) {
                    echo "No hay tareas para renombrar.\n";
                    break;
                }
                echo "Selecciona el padre de la tarea a renombrar:\n";
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

                echo "Selecciona la tarea a renombrar:\n";
                foreach ($tasks[$parent] as $index => $task) {
                    echo ($index + 1) . ". " . $task['name'] . "\n";
                }
                echo "Número de tarea: ";
                $taskIndex = intval(trim(fgets(STDIN))) - 1;
                if (!isset($tasks[$parent][$taskIndex])) {
                    echo "Selección no válida.\n";
                    break;
                }

                echo "Escribe el nuevo nombre de la tarea: ";
                $newTaskName = trim(fgets(STDIN));
                $tasks[$parent][$taskIndex]['name'] = $newTaskName;
                saveTasks($tasks);
                echo "Tarea renombrada con éxito.\n";

            } elseif ($renameOption === 2) {
                // Renombrar un padre
                if (empty($tasks)) {
                    echo "No hay padres para renombrar.\n";
                    break;
                }
                echo "Selecciona el padre a renombrar:\n";
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
                $oldParentName = $parents[$parentIndex];

                echo "Escribe el nuevo nombre del padre: ";
                $newParentName = trim(fgets(STDIN));

                if (isset($tasks[$newParentName])) {
                    echo "Ya existe un padre con ese nombre.\n";
                    break;
                }

                // Renombrar clave en el array
                $tasks[$newParentName] = $tasks[$oldParentName];
                unset($tasks[$oldParentName]);
                saveTasks($tasks);
                echo "Padre renombrado con éxito.\n";

            } else {
                echo "Opción no válida.\n";
            }
            break;

        case 10:
            // Verificar si hay tareas disponibles
            if (empty($tasks)) {
                echo "No hay tareas disponibles para reordenar.\n";
                break;
            }

            // Mostrar padres disponibles
            echo "Selecciona el padre de la tarea a mover:\n";
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

            // Verificar si hay tareas dentro del padre
            if (empty($tasks[$parent])) {
                echo "No hay tareas en este padre.\n";
                break;
            }

            // Mostrar las tareas disponibles
            echo "🐣 Selecciona la tarea a mover:\n";
            foreach ($tasks[$parent] as $index => $task) {
                echo ($index + 1) . ". " . $task['name'] . "\n";
            }

            echo "Número de tarea a mover: ";
            $taskIndex = intval(trim(fgets(STDIN))) - 1;
            if (!isset($tasks[$parent][$taskIndex])) {
                echo "Selección no válida.\n";
                break;
            }

            // Guardar la tarea a mover y eliminarla temporalmente
            $taskToMove = $tasks[$parent][$taskIndex];
            array_splice($tasks[$parent], $taskIndex, 1);

            // Mostrar nuevamente las tareas disponibles después de la eliminación
            echo "🦅 Selecciona la nueva posición para la tarea:\n";
            foreach ($tasks[$parent] as $index => $task) {
                echo ($index + 1) . ". " . $task['name'] . "\n";
            }
            echo ($index + 2) . ". Última posición\n"; // Opción para ponerla al final

            echo "Número de nueva posición: ";
            $newPosition = intval(trim(fgets(STDIN))) - 1;

            // Asegurar que la posición sea válida
            if ($newPosition < 0) {
                $newPosition = 0; // Si ingresa un número menor, se pone al inicio
            } elseif ($newPosition > count($tasks[$parent])) {
                $newPosition = count($tasks[$parent]); // Si es mayor, se pone al final
            }

            // Insertar la tarea en la nueva posición
            array_splice($tasks[$parent], $newPosition, 0, [$taskToMove]);

            // Guardar los cambios
            saveTasks($tasks);
            echo "Tarea movida con éxito.\n";
            break;

        case 11:
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
