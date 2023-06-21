<div class="mx-5">
    <div class="text-registrationPrimaryColor italic font-bold text-xl">
        Visitor details
    </div>

    <div class="mt-5 grid grid-cols-2 gap-y-3 gap-x-5">

        {{-- ROW 4 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-2 gap-x-5">
                <div class="col-span-2 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Company name
                    </div>
                    <div>
                        <input placeholder="Company name" type="text" wire:model.lazy="companyName"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('companyName')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Job title
                    </div>
                    <div>
                        <input placeholder="Job title" type="text" wire:model.lazy="jobTitle"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('jobTitle')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 2 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-11 gap-x-5">
                <div class="col-span-11 sm:col-span-2">
                    <div class="text-registrationPrimaryColor">
                        Salutation
                    </div>
                    <div>
                        <select wire:model.lazy="salutation"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value=""></option>
                            @foreach ($salutations as $salutation)
                                <option value="{{ $salutation }}">{{ $salutation }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-span-11 sm:col-span-3">
                    <div class="text-registrationPrimaryColor">
                        First name <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="First name" type="text" wire:model.lazy="firstName"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('firstName')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-11 sm:col-span-3">
                    <div class="text-registrationPrimaryColor">
                        Middle name
                    </div>
                    <div>
                        <input placeholder="Middle name" type="text" wire:model.lazy="middleName"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    </div>
                </div>

                <div class="col-span-11 sm:col-span-3">
                    <div class="text-registrationPrimaryColor">
                        Last name <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="Last name" type="text" wire:model.lazy="lastName"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('lastName')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 4 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-2 gap-x-5">
                <div class="col-span-2 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Email address <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="Email address" type="text" wire:model.lazy="emailAddress"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('emailAddress')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                        @if ($emailMainExistingError != null)
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $emailMainExistingError }}
                            </div>
                        @endif

                        @if ($emailMainAlreadyUsedError != null)
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $emailMainAlreadyUsedError }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Mobile number <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="xxxxxxx" type="text" wire:model.lazy="mobileNumber"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('mobileNumber')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 3 --}}
        <div class="space-y-2 col-span-2">
            <div class="grid grid-cols-3 gap-x-5">
                <div class="col-span-3 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Nationality <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <select wire:model.lazy="nationality"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value=""></option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}">
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>

                        @error('nationality')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-3 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        Country <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <select wire:model.lazy="country"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                            <option value=""></option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}">
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>

                        @error('country')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>


                <div class="col-span-3 sm:col-span-1">
                    <div class="text-registrationPrimaryColor">
                        City <span class="text-red-500">*</span>
                    </div>
                    <div>
                        <input placeholder="City" type="text" wire:model.lazy="city"
                            class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                        @error('city')
                            <div class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 5 --}}
        <div class="col-span-2 space-y-2">
            <div class="text-registrationPrimaryColor">
                Where did you hear about us?
            </div>
            <div>
                <select wire:model.lazy="heardWhere"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    <option value=""></option>
                    <option value="Email">Email</option>
                    <option value="Industry association">Industry association</option>
                    <option value="Media">Media</option>
                    <option value="Facbook">Facebook</option>
                    <option value="Twitter">Twitter</option>
                    <option value="YouTube">YouTube</option>
                    <option value="Instagram">Instagram</option>
                    <option value="LinkedIn">LinkedIn</option>
                </select>

                @error('heardWhere')
                    <div class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>
</div>

@if (!empty($additionalVisitors))
    <div class="mt-10 mx-5">
        <div class="text-registrationPrimaryColor italic font-bold text-xl">
            Additional visitor(s)
        </div>

        <div class="mt-5">
            @php $count = 2; @endphp
            @foreach ($additionalVisitors as $additionVisitor)
                <div class="bg-registrationCardBGColor px-5 py-2 mt-5 flex justify-between rounded-md">
                    <div>
                        <div class="font-bold text-2xl flex items-center gap-2 mt-1">
                            <p>{{ $additionVisitor['subSalutation'] }} {{ $additionVisitor['subFirstName'] }}
                                {{ $additionVisitor['subMiddleName'] }} {{ $additionVisitor['subLastName'] }}
                            </p>
                        </div>
                        <p class="mt-2"> {{ $additionVisitor['subEmailAddress'] }},
                            {{ $additionVisitor['subMobileNumber'] }}</p>
                        <p>Nationality: {{ $additionVisitor['subNationality'] }}</p>
                        <p>Country & City: {{ $additionVisitor['subCountry'] }} & {{ $additionVisitor['subCity'] }}
                        </p>
                        <p>Company name: {{ $additionVisitor['subCompanyName'] ?? 'N/A' }}</p>
                        <p>Job title: {{ $additionVisitor['subJobTitle'] ?? 'N/A' }}</p>
                    </div>
                    <div class="flex flex-col justify-between items-end">
                        <p class="text-registrationPrimaryColor font-bold">Visitor {{ $count }}</p>
                        <div class="flex gap-3">
                            <div wire:click.prevent="openEditModal('{{ $additionVisitor['subVisitorId'] }}')"
                                class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Edit
                            </div>

                            <div wire:click.prevent="removeAdditionalVisitor('{{ $additionVisitor['subVisitorId'] }}')"
                                class="cursor-pointer hover:text-red-600 text-red-500">
                                <i class="fa-solid fa-trash"></i>
                                Remove
                            </div>
                        </div>
                    </div>
                </div>

                @php $count++; @endphp
            @endforeach
        </div>
    </div>
@endif

<div class="mt-10 flex flex-col sm:flex-row gap-10 mx-5">
    <div class="col-span-1">

        @if ($showAddVisitorModal)
            @include('livewire.registration.visitor.modal.add_visitor_modal')
        @endif

        @if ($showEditVisitorModal)
            @include('livewire.registration.visitor.modal.edit_visitor_modal')
        @endif

        @if (
            $firstName != null &&
                $lastName != null &&
                $emailAddress != null &&
                $mobileNumber != null &&
                $nationality != null &&
                $country != null &&
                $city != null &&
                count($additionalVisitors) < 4)
            <button wire:click.prevent="openAddModal" type="button" wire:key="btnOpenAddModal"
                class="cursor-pointer hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor rounded-md py-4 px-10">+
                Add more</button>
        @else
            <button disabled type="button"
                class="cursor-not-allowed font-bold border-gray-600 border-2 bg-white text-gray-600  rounded-md py-4 px-10">+
                Add more</button>
        @endif
    </div>

    <div class="col-span-1">
        <div class="text-registrationPrimaryColor italic font-bold text-xl">
            Do you wish to invite more visitors?
        </div>

        <div class="text-registrationPrimaryColor italic text-sm mt-2 w-full sm:w-3/5">
            If you wish to register more than 5 visitors, please contact our sales team at
            forumregistration@gpca.org.ae or call +971 4 5106666 ext. 153
        </div>
    </div>
</div>
