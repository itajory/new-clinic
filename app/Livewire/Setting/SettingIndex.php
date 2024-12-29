<?php

namespace App\Livewire\Setting;

use App\Models\Setting;
use Livewire\Component;
use Illuminate\Support\Arr;
use Livewire\WithFileUploads;
use App\Livewire\Forms\SettingsForm;

class SettingIndex extends Component
{
    use WithFileUploads;

    public $logoFile;
    public $logoPreview;
    public SettingsForm $form;
    public $selectedLanguages = [];

    public function mount()
    {
        $settings = Setting::all();
        $this->form->setSettings($settings);

        // Initialize selectedLanguages
        $languageSetting = collect($this->form->settings)->firstWhere('key', 'languages');
        if ($languageSetting) {
            $this->selectedLanguages = explode(',', $languageSetting['value']);
        }
    }

    public function render()
    {
        return view('livewire.setting.setting-index')->layout('layouts.dash');
    }

    public function updateSetting($index, $value)
    {
        $this->form->updateSetting($index, $value);
    }

    public function updatedSelectedLanguages()
    {
        $languageIndex = array_search('languages', array_column($this->form->settings, 'key'));
        if ($languageIndex !== false) {
            $this->form->settings[$languageIndex]['value'] = implode(',', $this->selectedLanguages);
        }
    }
    public function updatedLogoFile()
    {
        $this->validate([
            'logoFile' => 'image|max:1024', // 1MB Max
        ]);

        $this->logoPreview = $this->logoFile->temporaryUrl();
    }

    public function save()
    {
        if ($this->logoFile) {
            $path = $this->logoFile->store('logos', 'public');
            $this->form->settings = collect($this->form->settings)->map(function ($setting) use ($path) {
                if ($setting['key'] === 'logo') {
                    $setting['value'] = $path;
                }
                return $setting;
            })->toArray();
        }


        $this->form->save();
        $this->dispatch('settings-saved');


        $this->logoFile = null;
        $this->logoPreview = null;
    }
}
