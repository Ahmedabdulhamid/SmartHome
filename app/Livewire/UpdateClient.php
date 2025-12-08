<?php

namespace App\Livewire;

use App\Models\Client; // تأكد من استيراد نموذج العميل الخاص بك
use Livewire\Component;

class UpdateClient extends Component
{
    public $name;
    public $email;
    public $phone;
    public $address;
    public Client $client; // استخدم Type-Hinting لنوع Client إذا أمكن

    // قواعد التحقق من الصحة
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:clients,email,NULL,id', // 'clients' هو اسم جدولك
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
    ];

    public function mount(Client $client) // يجب أن تتوقع نموذج Client هنا
    {
        $this->client = $client;
        // قم بتعيين قاعدة الـ 'unique' لتجاهل العميل الحالي
        $this->rules['email'] = 'required|email|max:255|unique:clients,email,' . $client->id;

        // تعيين قيم الحقول من النموذج (وهذا يجعل كل input يأخذ قيمته)
        $this->name = $client->name;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->address = $client->address;
    }

    public function update()
    {
        // 1. التحقق من صحة البيانات
        $this->validate();

        // 2. تحديث بيانات العميل
        $this->client->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        // 3. إرسال حدث لإغلاق المودال وتحديث قائمة العملاء
        $this->dispatch('clientUpdated', ['message' => 'Client updated successfully.']);

       return redirect()->route('clients.index');
    }

    public function render()
    {
        return view('livewire.update-client');
    }
}
