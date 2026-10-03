<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateGroupServices extends Component
{
    public $GroupsItems = [];
    public $allServices = [];
    public $discount_value = 0;
    public $taxes = 17;
    public $name_group;
    public $notes;
    public $ServiceSaved = false;
    public $ServiceUpdated = false;
    public $show_table = true;
    public $updateMode = false;
    public $group_id;

    public function mount()
    {
        $this->allServices = Service::all();
    }

    public function render()
    {

        if (!is_array($this->GroupsItems)) {
            $this->GroupsItems = [];
        }

        $total = 0;
        foreach ($this->GroupsItems as $groupItem) {
            if (!is_array($groupItem) || empty($groupItem['is_saved']) || !isset($groupItem['service_id'], $groupItem['quantity']) || !is_numeric($groupItem['quantity']) || $groupItem['quantity'] < 1) {
                continue;
            }
            $service = Service::find($groupItem['service_id']);
            if ($service) {
                $total += $service->price * $groupItem['quantity'];
            }
        }

        return view('livewire.GroupServices.create-group-services', [
            'groups' => Group::all(),
            'subtotal' => $Total_after_discount = $total - ((is_numeric($this->discount_value) ? $this->discount_value : 0)),
            'total' => $Total_after_discount * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100)
        ]);

    }


    public function addService()
    {
        if (!is_array($this->GroupsItems) || count($this->GroupsItems) >= 100) {
            $this->addError('GroupsItems', trans('validation.max.array', ['max' => 100]));
            return;
        }
        foreach ($this->GroupsItems as $key => $groupItem) {
            if (!$groupItem['is_saved']) {
                $this->addError('GroupsItems.' . $key, 'يجب حفظ هذا الخدمة قبل إنشاء خدمة جديدة.');
                return;
            }
        }

        $this->GroupsItems[] = [
            'service_id' => '',
            'quantity' => 1,
            'is_saved' => false,
            'service_name' => '',
            'service_price' => 0
        ];

        $this->ServiceSaved = false;
    }

    public function editService($index)
    {
        if (!is_numeric($index) || !array_key_exists((int) $index, $this->GroupsItems)) {
            $this->addError('GroupsItems', trans('validation.exists'));
            return;
        }
        $index = (int) $index;
        foreach ($this->GroupsItems as $key => $groupItem) {
            if (!$groupItem['is_saved']) {
                $this->addError('GroupsItems.' . $key, 'This line must be saved before editing another.');
                return;
            }
        }

        $this->GroupsItems[$index]['is_saved'] = false;
    }


    public function saveService($index)
    {
        $this->resetErrorBag();
        if (!is_numeric($index) || !array_key_exists((int) $index, $this->GroupsItems)) {
            $this->addError('GroupsItems', trans('validation.exists'));
            return;
        }
        $index = (int) $index;
        $this->validate([
            "GroupsItems.$index.service_id" => ['required', 'integer', 'exists:Services,id'],
            "GroupsItems.$index.quantity" => ['required', 'integer', 'min:1', 'max:1000'],
        ]);
        $product = Service::findOrFail($this->GroupsItems[$index]['service_id']);
        $this->GroupsItems[$index]['service_name'] = $product->name;
        $this->GroupsItems[$index]['service_price'] = $product->price;
        $this->GroupsItems[$index]['is_saved'] = true;
    }

    public function removeService($index)
    {
        if (!is_array($this->GroupsItems) || !is_numeric($index) || !array_key_exists((int) $index, $this->GroupsItems)) {
            $this->addError('GroupsItems', trans('validation.exists'));
            return;
        }
        unset($this->GroupsItems[(int) $index]);
        $this->GroupsItems = array_values($this->GroupsItems);
    }

    public function saveGroup()
    {

        $this->validate([
            'name_group' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'taxes' => ['required', 'numeric', 'between:0,100'],
            'GroupsItems' => ['required', 'array', 'min:1', 'max:100'],
            'GroupsItems.*.service_id' => ['required', 'integer', 'exists:Services,id'],
            'GroupsItems.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $groupTotal = 0;
        foreach ($this->GroupsItems as $index => $item) {
            $service = Service::findOrFail($item['service_id']);
            $this->GroupsItems[$index]['service_name'] = $service->name;
            $this->GroupsItems[$index]['service_price'] = $service->price;
            $this->GroupsItems[$index]['is_saved'] = true;
            $groupTotal += $service->price * $item['quantity'];
        }
        if ($groupTotal > 999999.99) {
            $this->addError('GroupsItems', trans('validation.max.numeric', ['max' => 999999.99]));
            return;
        }
        $this->validate(['discount_value' => ['required', 'numeric', 'min:0', 'lte:' . $groupTotal]]);
        $groupTotalWithTax = ($groupTotal - $this->discount_value) * (1 + $this->taxes / 100);
        if ($groupTotalWithTax > 999999.99) {
            $this->addError('taxes', 'The group total exceeds the maximum supported amount.');
            return;
        }

        // update
        if ($this->updateMode) {
            $this->validate(['group_id' => ['required', 'integer', 'exists:groups,id']]);
            $Groups = Group::findOrFail($this->group_id);
            $total = 0;
            foreach ($this->GroupsItems as $groupItem) {
                if ($groupItem['is_saved'] && $groupItem['service_price'] && $groupItem['quantity']) {
                    // الاجمالي قبل الخصم
                    $total += $groupItem['service_price'] * $groupItem['quantity'];
                }
            }
            //الاجمالي قبل الخصم
            $Groups->Total_before_discount = $total;
            // قيمة الخصم
            $Groups->discount_value = $this->discount_value;
            // الاجمالي بعد الخصم
            $Groups->Total_after_discount = $total - ((is_numeric($this->discount_value) ? $this->discount_value : 0));
            //  نسبة الضريبة
            $Groups->tax_rate = $this->taxes;
            // الاجمالي + الضريبة
            $Groups->Total_with_tax = $Groups->Total_after_discount * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100);
            $Groups->save();
            // حفظ الترجمة
            $Groups->name = $this->name_group;
            $Groups->notes = $this->notes;
            $Groups->save();
            // حفظ العلاقة
            $Groups->service_group()->detach();
            foreach ($this->GroupsItems as $GroupsItem) {
                $Groups->service_group()->attach($GroupsItem['service_id'], ['quantity' => $GroupsItem['quantity']]);
            }

            $this->ServiceSaved = false;
            $this->ServiceUpdated = true;

        } else {

            // insert
            $Groups = new Group();
            $total = 0;

            foreach ($this->GroupsItems as $groupItem) {
                if ($groupItem['is_saved'] && $groupItem['service_price'] && $groupItem['quantity']) {
                    // الاجمالي قبل الخصم
                    $total += $groupItem['service_price'] * $groupItem['quantity'];
                }
            }

            //الاجمالي قبل الخصم
            $Groups->Total_before_discount = $total;
            // قيمة الخصم
            $Groups->discount_value = $this->discount_value;
            // الاجمالي بعد الخصم
            $Groups->Total_after_discount = $total - ((is_numeric($this->discount_value) ? $this->discount_value : 0));
            //  نسبة الضريبة
            $Groups->tax_rate = $this->taxes;
            // الاجمالي + الضريبة
            $Groups->Total_with_tax = $Groups->Total_after_discount * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100);
            $Groups->save();

            // حفظ الترجمة
            $Groups->name = $this->name_group;
            $Groups->notes = $this->notes;
            $Groups->save();

            // حفظ العلاقة
            foreach ($this->GroupsItems as $GroupsItem) {
                $Groups->service_group()->attach($GroupsItem['service_id'], ['quantity' => $GroupsItem['quantity']]);
            }

            $this->reset('GroupsItems', 'name_group', 'notes');
            $this->discount_value = 0;
            $this->ServiceSaved = true;

        }



    }

    public function show_form_add()
    {
        $this->show_table = false;
    }

    public function edit($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:groups,id']])->validate();
        $this->show_table = false;
        $this->updateMode = true;
        $group = Group::findOrFail($id);
        $this->group_id = $id;

        $this->reset('GroupsItems', 'name_group', 'notes');
        $this->name_group = $group->name;
        $this->notes = $group->notes;

        $this->discount_value = intval($group->discount_value);
        $this->ServiceSaved = false;

        foreach ($group->service_group as $serviceGroup) {
            $this->GroupsItems[] = [
                'service_id' => $serviceGroup->id,
                'quantity' => $serviceGroup->pivot->quantity,
                'is_saved' => true,
                'service_name' => $serviceGroup->name,
                'service_price' => $serviceGroup->price
            ];
        }
    }

    public function delete($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:groups,id']])->validate();
        Group::destroy($id);

        return redirect()
            ->to('/Add_GroupServices')
            ->with('success', 'تم حذف المجموعة بنجاح');
    }

}
