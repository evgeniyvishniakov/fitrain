<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Language;

class CheckLanguages extends Command
{
    protected $signature = 'check:languages';
    protected $description = 'Check existing languages';

    public function handle()
    {
        $languages = Language::all(['id', 'code', 'name', 'is_active']);
        
        $this->info('Existing languages:');
        foreach ($languages as $language) {
            $status = $language->is_active ? 'Active' : 'Inactive';
            $this->line("ID: {$language->id} | Code: {$language->code} | Name: {$language->name} | Status: {$status}");
        }
        
        return 0;
    }
}







