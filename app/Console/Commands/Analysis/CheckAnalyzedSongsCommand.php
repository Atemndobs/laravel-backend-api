<?php

namespace App\Console\Commands\Analysis;

use App\Models\Song;
use Illuminate\Console\Command;

class CheckAnalyzedSongsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:status {--a|analyzed} {--s|status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $songs = Song::all();

        $analyzed_stats = [];
        $status_stats = [];
        if ($this->option('analyzed')) {
            $analyzed = $songs->where('analyzed', true);
            $pending = $songs->where('analyzed', false);
            $queued = $songs->where('status', '=', 'queued');
            $tot = $analyzed->count() + $pending->count();

            $analyzed_stats = [
                'analyzed' => $analyzed->count(),
                'no_analyzed' => $pending->count(),
                'queued' => $queued->count(),
                'total' => $tot,
            ];
        }
        if ($this->option('status')) {
            $status = $songs->where('status', '!=', 're-classified');
            $re_classified = $songs->where('status', '=', 're-classified');
            $deleted= $songs->where('status', '=', 'deleted');
            $queued = $songs->where('status', '=', 'queued');
            $tot=
                $re_classified->count()
                + $deleted->count()
                + $queued->count();

            $status_stats = [
                'not_yet_re-classified' => $status->count(),
                're-classified' => $re_classified->count(),
                'deleted' => $deleted->count(),
                'queued' => $queued->count(),
                'total' => $tot,
            ];

        }

        if ($this->option('analyzed') && $this->option('status')) {
            $this->info('Analyzed Stats');
            $this->table(['analyzed', "<fg=magenta> not_analyzed </>",  "<fg=red> queued </>", 'total'], [$analyzed_stats]);
            $this->info('Status Stats');
            $this->table(['not_yet_re-classified', 're-classified', 'deleted', "<fg=red> queued </>",'total'], [$status_stats]);
        } elseif ($this->option('analyzed')) {
            $this->info('Analyzed Stats');
            $this->table(['analyzed', "<fg=magenta> not_analyzed </>",  "<fg=red> queued </>", 'total'], [$analyzed_stats]);
        } elseif ($this->option('status')) {
            $this->info('Status Stats');
            $this->table(['not_yet_re-classified', 're-classified', 'deleted', "<fg=red> queued </>",'total'], [$status_stats]);
        } else {
            $this->info('Total: ' . count($songs));
        }
        return 0;
    }
}
