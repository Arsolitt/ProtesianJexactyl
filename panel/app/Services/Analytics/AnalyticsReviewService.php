<?php

namespace Jexactyl\Services\Analytics;

use Jexactyl\Models\AnalyticsData;
use Jexactyl\Models\AnalyticsMessage;
use Jexactyl\Models\Server;

class AnalyticsReviewService
{
    /**
     * Reviews analytics data for a server and sends
     * messages to the UI containing recommendations and information.
     */
    public function handle(Server $server)
    {
        $total = ['cpu' => 0, 'memory' => 0, 'disk' => 0];
        $analytics = AnalyticsData::where('server_id', $server->id)->select('cpu', 'memory', 'disk')->get();
        $size = count($analytics);

        foreach ($analytics as $data) {
            $total['cpu'] += $data->cpu;
            $total['memory'] += $data->memory;
            $total['disk'] += $data->disk;
        }

        $total['cpu'] /= $size;
        $total['memory'] /= $size;
        $total['disk'] /= $size;

        $this->calculate($server->id, $total);
    }

    /**
     * Calculates the average usage per resource and sends notifications
     * when a conditional statement is true.
     */
    public function calculate(int $id, array $values): void
    {
        $min = 25;
        $max = 50;
        $size = count($values);

        foreach ($values as $key => $value) {
            if ($value <= 30) {
                $this->message($id, $size, $key, $value, false);
            }

            if ($value > 75) {
                $this->message($id, $size, $key, $value, true);
            }
        }
    }

    /**
     * Sends a message to the server analytics UI.
     */
    public function message(int $id, int $size, string $key, int $value, bool $warning): void
    {
        $suffix = '% за последние ' . $size . ' проверки';

        AnalyticsMessage::create([
            'server_id' => $id,
            'title' => 'Использвание ' . $this->getNameBuyType($key). ' ' . ($warning ? 'выше 75' : 'ниже 30') . '%',
            'content' => 'Средний показатель ' . $value . $suffix,
            'type' => ($warning ? 'warning' : 'success'),
        ]);
    }

    private function getNameBuyType(string $type): string
    {
        return match ($type) {
            'cpu' => 'процессора',
            'memory' => 'ОЗУ',
            'disk' => 'диска',
        };
    }
}
