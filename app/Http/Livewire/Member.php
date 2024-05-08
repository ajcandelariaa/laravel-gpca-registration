<?php

namespace App\Http\Livewire;

use App\Models\Member as Members;
use Livewire\Component;
use Livewire\WithFileUploads;

class Member extends Component
{
    use WithFileUploads;

    public $members, $name, $logo, $sector, $type;
    public $updateMember = false;
    public $oldImage, $memberId;
    public $searchTerm;
    public $companySectors;
    public $delete_id;
    public $showImportModal = false;
    public $csvFile;

    public $insertedData = [];

    public $csvFileError;

    protected $listeners = ['deleteMemberConfirmed' => 'deleteMember', 'importMemberConfirmed' => 'submitImportMember', 'deleteAllMembersConfirmed' => 'deleteAllMembers'];

    public function render()
    {
        $this->companySectors = config('app.companySectors');
        if (empty($this->searchTerm)) {
            $fullMembers = Members::orderBy('name', 'ASC')->where('type', 'full')->get();
            $associateMembers = Members::orderBy('name', 'ASC')->where('type', 'associate')->get();
            $this->members = $fullMembers->merge($associateMembers);
        } else {
            $this->members = Members::where('name', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('sector', 'like', '%' . $this->searchTerm . '%')
                ->orderBy('created_at', 'ASC')
                ->get();
        }
        return view('livewire.admin.members.member');
    }

    public function addMember()
    {
        $image = null;
        $this->validate(
            [
                'name' => 'required',
                'type' => 'required',
                'logo' => 'nullable|mimes:jpeg,png,jpg,gif',
            ],
            [
                'name.required' => 'Name is required',
                'type.required' => 'Member type is required',
                'logo.mimes' => 'Logo must be in jpeg, png, jpg, gif format',
            ]
        );


        if ($this->logo != null) {
            $image = $this->logo->store('public/members');
        }

        Members::create([
            'name' => $this->name,
            'sector' => $this->sector,
            'type' => $this->type,
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
        $this->type = $member->type;
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
                'type' => 'required',
                'logo' => 'nullable|mimes:jpeg,png,jpg,gif',
            ],
            [
                'name.required' => 'Name is required',
                'type.required' => 'Member type is required',
                'logo.mimes' => 'Logo must be in jpeg, png, jpg, gif format',
            ]
        );

        if ($this->logo != null) {
            $image = $this->logo->store('public/members');
            Members::find($this->memberId)->fill([
                'name' => $this->name,
                'sector' => $this->sector,
                'type' => $this->type,
                'logo' => $image,
            ])->save();
        } else {
            Members::find($this->memberId)->fill([
                'name' => $this->name,
                'sector' => $this->sector,
                'type' => $this->type,
            ])->save();
        }

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
        $this->csvFileError = null;
        $this->showImportModal = false;
    }

    public function importMemberConfirmation()
    {
        $this->validate([
            'csvFile' => 'required|mimes:csv,txt',
        ]);

        $file = fopen($this->csvFile->getRealPath(), "r", 'UTF-8');
        $rows = [];

        $rowCounter = 0;
        while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
            if ($rowCounter > 0) {
                $tempRow = [];
                foreach ($row as $col) {
                    $tempRow[] = trim($col);
                }
                $row = $tempRow;
            }

            $rowCounter++;
            $rows[] = $row;
        }
        fclose($file);
        // dd($rows);

        $checkIfCorrectFormat = true;
        for ($i = 0; $i < count($rows); $i++) {
            if ($i == 0) {
                if (count($rows[$i]) == 2) {
                    if ($rows[$i][0] != "Company Name" || $rows[$i][1] != "Type") {
                        $checkIfCorrectFormat = false;
                    }
                } else {
                    $checkIfCorrectFormat = false;
                }
                break;
            }
        }

        if ($checkIfCorrectFormat) {
            $this->csvFileError = null;
            $this->insertedData = $rows;
            $this->dispatchBrowserEvent('swal:import-member-confirmation', [
                'type' => 'warning',
                'message' => 'Are you sure?',
                'text' => "",
            ]);
        } else {
            $this->csvFileError = "File is not valid, please make sure you have the correct format.";
        }
    }

    public function submitImportMember()
    {
        for ($i = 0; $i < count($this->insertedData); $i++) {
            if ($i == 0) {
                continue;
            } else {
                Members::create([
                    'name' => $this->insertedData[$i][0],
                    'type' => $this->insertedData[$i][1],
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

    public function deleteAllMembersClicked()
    {
        $this->dispatchBrowserEvent('swal:delete-all-members-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function deleteAllMembers()
    {
        Members::truncate();

        $this->dispatchBrowserEvent('swal:delete-all-members', [
            'type' => 'success',
            'message' => 'Members Deleted Successfully!',
            'text' => ''
        ]);
    }
}
