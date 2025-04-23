<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Province;
use Livewire\Component;
use Livewire\WithPagination;

class GeographyManagement extends Component
{
    use WithPagination;

    // Province Properties
    public $provinceName = '';
    public $provinceId = null;
    public $showProvinceModal = false;

    // City Properties
    public $cityName = '';
    public $selectedProvinceId = null;
    public $cityId = null;
    public $showCityModal = false;

    // Listeners to refresh data when modals close
    protected $listeners = ['provinceModalClosed' => 'refreshData', 'cityModalClosed' => 'refreshData'];

    public function refreshData()
    {
        // This method can be called to force a re-render if needed
        // Often not necessary as Livewire handles reactivity
    }

    // --- Province Methods ---

    public function createProvince()
    {
        $this->resetProvinceInput();
        $this->showProvinceModal = true;
    }

    public function editProvince(Province $province)
    {
        $this->provinceId = $province->id;
        $this->provinceName = $province->name;
        $this->showProvinceModal = true;
    }

    public function saveProvince()
    {
        $validatedData = $this->validate([
            'provinceName' => 'required|string|max:255|unique:provinces,name,' . $this->provinceId,
        ]);

        Province::updateOrCreate(['id' => $this->provinceId], ['name' => $this->provinceName]);

        session()->flash('message', $this->provinceId ? 'Province Updated.' : 'Province Created.');
        $this->closeProvinceModal();
    }

    public function deleteProvince(Province $province)
    {
        // Consider adding check if province has cities before deleting
        if ($province->cities()->count() > 0) {
             session()->flash('error', 'Cannot delete province with associated cities.');
             return;
        }
        $province->delete();
        session()->flash('message', 'Province Deleted.');
    }

    public function closeProvinceModal()
    {
        $this->showProvinceModal = false;
        $this->resetProvinceInput();
        $this->dispatch('provinceModalClosed');
    }

    private function resetProvinceInput()
    {
        $this->provinceName = '';
        $this->provinceId = null;
        $this->resetErrorBag();
    }

    // --- City Methods ---

     public function createCity()
    {
        $this->resetCityInput();
        $this->showCityModal = true;
    }

    public function editCity(City $city)
    {
        $this->cityId = $city->id;
        $this->cityName = $city->name;
        $this->selectedProvinceId = $city->province_id;
        $this->showCityModal = true;
    }

     public function saveCity()
    {
        $validatedData = $this->validate([
            'cityName' => 'required|string|max:255',
            'selectedProvinceId' => 'required|exists:provinces,id',
            // Add unique constraint for city within a province?
            // Rule::unique('cities', 'name')->where('province_id', $this->selectedProvinceId)->ignore($this->cityId),
        ]);

        City::updateOrCreate(['id' => $this->cityId], [
            'name' => $this->cityName,
            'province_id' => $this->selectedProvinceId,
        ]);

        session()->flash('message', $this->cityId ? 'City Updated.' : 'City Created.');
        $this->closeCityModal();
    }

     public function deleteCity(City $city)
    {
         // Consider adding check if city is linked to providers before deleting
        $city->delete();
        session()->flash('message', 'City Deleted.');
    }

    public function closeCityModal()
    {
        $this->showCityModal = false;
        $this->resetCityInput();
         $this->dispatch('cityModalClosed');
    }

     private function resetCityInput()
    {
        $this->cityName = '';
        $this->selectedProvinceId = null;
        $this->cityId = null;
        $this->resetErrorBag();
    }


    // --- Render Method ---

    public function render()
    {
        $provinces = Province::orderBy('name')->get();
        // Paginate cities, maybe allow filtering by province later
        $cities = City::with('province')->orderBy('province_id')->orderBy('name')->paginate(15);

        return view('livewire.admin.geography-management', [
            'provinces' => $provinces,
            'cities' => $cities,
        ])->layout('layouts.admin');
    }
}
