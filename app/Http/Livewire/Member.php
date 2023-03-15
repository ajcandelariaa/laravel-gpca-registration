<?php

namespace App\Http\Livewire;

use App\Models\Member as Members;
use Livewire\Component;
use Livewire\WithFileUploads;

class Member extends Component
{
    use WithFileUploads;

    public $members, $name, $logo, $sector;
    public $updateMember = false;
    public $oldImage, $memberId;
    public $companySectors = [
        'Academia / Educational & Research Institutes / Universities',
        'Brand owners',
        'Catalyst or Additive Manufacturers ',
        'Chemical / Petrochemical Producers    ',
        'Chemical Traders / Distributors ',
        'Engineering Company / EPC Contractors',
        'Equipment Manufacturers',
        'Governments & Regulators',
        'Industry Associations',
        'Investment / Financial / Audit / Insurance Firms',
        'Legal firms',
        'Logistics Service Providers',
        'NGOs',
        'Oil & Gas (Upstream) ',
        'Petroleum Producers / Refineries / Gas processing plants',
        'Plastics Convertors',
        'Power & Utilities',
        'Press/media ',
        'Retailers',
        'Shipping Lines',
        'Strategy Consultancies ',
        'Technology Consultancies',
        'Technology Services Providers',
        'Terminal Operators',
        'Venture Capitalists ',
        'Waste Management & Recycling',
    ];

    protected $listeners = [
        'deleteMemberScript' => 'deleteMember'
    ];

    public function render()
    {
        $this->members = Members::orderBy('name', 'ASC')->get();
        return view('livewire.member');
    }

    public function addMember()
    {
        $image = null;
        $this->validate([
            'name' => 'required',
            'logo' => 'sometimes|image||dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'
        ]);

        try {
            if ($this->logo != null) {
                $image = $this->logo->store('public/members');
            }

            Members::create([
                'name' => $this->name,
                'sector' => $this->sector,
                'logo' => $image,
                'active' => true,
            ]);
            $this->reset();
            session()->flash('added', 'Member added successfully!!');
        } catch (\Exception $e) {
            session()->flash('error_added', 'Something goes wrong while adding a member');
            $this->reset();
        }
    }

    public function updateStatus($memberId, $memberActive)
    {
        try {
            Members::find($memberId)->fill(
                [
                    'active' => !$memberActive,
                ],
            )->save();
            $memberActive ? $message = "Inactive" : $message = "Active";
            session()->flash('updated_status', "Status updated into $message.");
        } catch (\Exception $e) {
            session()->flash('error_updating_status', 'Something goes wrong while changing the status!');
        }
    }

    public function showEditMember($memberId)
    {
        $this->logo = null;
        
        $member = Members::findOrFail($memberId);
        $this->name = $member->name;
        $this->sector = $member->sector;
        $this->oldImage = $member->logo;
        $this->memberId = $member->id;
        $this->updateMember = true;
    }

    public function hideEditMember()
    {
        $this->updateMember = false;
        $this->reset();
    }

    public function updateMember()
    {
        $image = null;

        $this->validate([
            'name' => 'required',
            'logo' => 'nullable|image||dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'
        ]);

        try {
            if ($this->logo != null) {
                $image = $this->logo->store('public/members');
                Members::find($this->memberId)->fill([
                    'name' => $this->name,
                    'sector' => $this->sector,
                    'logo' => $image,
                ])->save();
            } else {
                Members::find($this->memberId)->fill([
                    'name' => $this->name,
                    'sector' => $this->sector,
                ])->save();
            }

            session()->flash('member_updated', 'Member updated successfully!!');
            $this->reset();
        } catch (\Exception $e) {
            session()->flash('error_updating_member', 'Something goes wrong while updating member');
            $this->reset();
        }
    }

    public function deleteMember($memberId)
    {
        try {
            Members::find($memberId)->delete();
            session()->flash('member_deleted', 'Member deleted successfully!!');
        } catch (\Exception $e) {
            session()->flash('error_deleting_member', 'Something goes wrong while deleting member');
        }
    }
}
