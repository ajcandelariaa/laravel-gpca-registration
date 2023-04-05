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
    public $searchTerm;
    public $companySectors;
    public $delete_id;
    public $showImportModal = false;
    public $csvFile;

    public $csvFileError;

    protected $listeners = ['deleteConfirmed' => 'deleteMember', 'importConfirmed' => 'submitImportMember'];

    public function render()
    {
        $this->companySectors = config('app.companySectors');
        if (empty($this->searchTerm)) {
            $this->members = Members::orderBy('name', 'ASC')->get();
        } else {
            $this->members = Members::where('name', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('sector', 'like', '%' . $this->searchTerm . '%')
                ->orderBy('name', 'ASC')
                ->get();
        }
        return view('livewire.members.member');
    }

    public function addMember()
    {
        $image = null;
        $this->validate(
            [
                'name' => 'required',
                'logo' => 'nullable|mimes:jpeg,png,jpg,gif',
            ],
            [
                'name.required' => 'Name is required',
                'logo.mimes' => 'Logo must be in jpeg, png, jpg, gif format',
            ]
        );


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

        $this->dispatchBrowserEvent('swal:add-member', [
            'type' => 'success',
            'message' => 'Member Added Successfully!',
            'text' => ''
        ]);
    }

    public function updateStatus($memberId, $memberActive)
    {
        Members::find($memberId)->fill(
            [
                'active' => !$memberActive,
            ],
        )->save();
        $memberActive ? $message = "Inactive" : $message = "Active";
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

        $this->validate(
            [
                'name' => 'required',
                'logo' => 'nullable|mimes:jpeg,png,jpg,gif',
            ],
            [
                'name.required' => 'Name is required',
                'logo.mimes' => 'Logo must be in jpeg, png, jpg, gif format',
            ]
        );

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

        $this->dispatchBrowserEvent('swal:update-member', [
            'type' => 'success',
            'message' => 'Member Updated Successfully!',
            'text' => ''
        ]);
    }

    public function deleteMemberConfirmation($memberId)
    {
        $this->delete_id = $memberId;
        $this->dispatchBrowserEvent('swal:delete-member-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "You won't be able to revert this!",
        ]);
    }
    public function deleteMember()
    {
        Members::find($this->delete_id)->delete();
        $this->dispatchBrowserEvent('swal:delete-member', [
            'type' => 'success',
            'message' => 'Member Deleted Successfully!',
            'text' => ''
        ]);
        $this->delete_id = null;
    }


    public function openImportModal()
    {
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->csvFile = null;
        $this->showImportModal = false;
    }

    public function importMemberConfirmation()
    {
        $this->validate([
            'csvFile' => 'required',
        ]);

        $file = fopen($this->csvFile->getRealPath(), "r");
        $rows = [];
        while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
            $rows[] = $row;
        }
        fclose($file);

        $checkIfCorrectFormat = true;
        for ($i = 0; $i < count($rows); $i++) {
            if ($i == 0) {
                if($rows[$i][0] != "Company Name" && $rows[$i][1] != "Company Sectors"){
                    $checkIfCorrectFormat = false;
                }
                break;
            }
        }

        if($checkIfCorrectFormat){
            $this->dispatchBrowserEvent('swal:import-member-confirmation', [
                'type' => 'warning',
                'message' => 'Are you sure?',
                'text' => "",
            ]);
            $this->csvFileError = null;
        } else {
            // PUT ERROR
            $this->csvFileError = "File is not valid, please make sure you have the correct format.";
        }

    }

    public function submitImportMember()
    {
        $file = fopen($this->csvFile->getRealPath(), "r");
        $rows = [];
        while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
            $rows[] = $row;
        }
        fclose($file);

        for ($i = 0; $i < count($rows); $i++) {
            if ($i == 0) {
                continue;
            } else {
                Members::create([
                    'name' => $rows[$i][0],
                    'sector' => ($rows[$i][1] == "") ? null : $rows[$i][1],
                    'active' => true,
                ]);
            }
        }

        $this->csvFile = null;
        $this->showImportModal = false;

        $this->dispatchBrowserEvent('swal:import-member', [
            'type' => 'success',
            'message' => 'Members Imported Successfully!',
            'text' => ''
        ]);
    }
}
