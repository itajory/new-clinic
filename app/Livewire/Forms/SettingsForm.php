<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Setting;
use Livewire\Attributes\Validate;
use Illuminate\Support\Collection;

class SettingsForm extends Form
{
    #[Validate('required|array')]
    public array $settings = [];

    public function rules()
    {
        return [
            'settings' => 'required|array',
            'settings.*.value' => 'required',
        ];
    }
    public function validationAttributes()
    {
        $attributes = [];
        foreach ($this->settings as $index => $setting) {
            $attributes["settings.{$index}.value"] = trans($setting['key']);
        }
        return $attributes;
    }
    // public function setSettings(Collection $settings)
    // {
    //     $this->settings = $settings->map(function ($setting) {
    //         return [
    //             'id' => $setting->id,
    //             'key' => $setting->key,
    //             'value' => $setting->value,
    //         ];
    //     })->toArray();
    // }
    public function setSettings($settings)
    {
        $this->settings = $settings->map(function ($setting) {
            return [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->value,
            ];
        })->toArray();
    }

    // public function updateSetting($index, $value)
    // {
    //     $this->settings[$index]['value'] = $value;
    // }
    public function updateSetting($index, $value)
    {
        $this->settings[$index]['value'] = $value;
    }

    // public function save()
    // {
    //     $this->validate();

    //     foreach ($this->settings as $setting) {
    //         Setting::where('id', $setting['id'])->update(['value' => $setting['value']]);
    //     }
    // }

    public function save()
    {
        $this->validate();

        foreach ($this->settings as $setting) {
            Setting::where('id', $setting['id'])->update(['value' => $setting['value']]);
        }
    }
}
