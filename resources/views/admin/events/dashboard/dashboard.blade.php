@extends('admin.layouts.master')

@section('content')
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>

    <div class="container mx-auto my-10">
        <div class="grid grid-cols-1 gap-4 text-center">
            <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Confirmed delegates</h3>
                <p class="text-4xl font-bold text-white">{{ $totalConfirmedDelegates }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-center mt-10">
            <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Total delegates</h3>
                <p class="text-4xl font-bold text-white">{{ $totalDelegates }}</p>
            </div>
            <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Registered today</h3>
                <p class="text-4xl font-bold text-white">{{ $totalRegisteredToday }}</p>
            </div>
        </div>


        <div class="grid grid-cols-3 gap-4 text-center mt-10">
            <div class="bg-green-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Total amount paid</h3>
                <p class="text-4xl font-bold text-white">$ {{ number_format($totalAmountPaid, 2, '.', ',') }}</p>
            </div>
            <div class="bg-green-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Paid today</h3>
                <p class="text-4xl font-bold text-white">{{ $totalPaidToday }}</p>
            </div>
            <div class="bg-green-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Revenue today</h3>
                <p class="text-4xl font-bold text-white">$ {{ number_format($totalAmountPaidToday, 2, '.', ',') }}</p>
            </div>
        </div>


        <div class="grid grid-cols-3 gap-4 text-center mt-10">
            <div class="bg-yellow-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Full Member</h3>
                <p class="text-4xl font-bold text-white">{{ $totalFullMember }}</p>
            </div>
            <div class="bg-yellow-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Member</h3>
                <p class="text-4xl font-bold text-white">{{ $totalMember }}</p>
            </div>
            <div class="bg-yellow-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Non-Member</h3>
                <p class="text-4xl font-bold text-white">{{ $totalNonMember }}</p>
            </div>
        </div>


        <div class="grid grid-cols-8 gap-4 mt-10">
            <div class="bg-emerald-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Paid</h3>
                <p class="text-4xl font-bold text-white">{{ $totalPaid }}</p>
            </div>
            <div class="bg-emerald-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Free</h3>
                <p class="text-4xl font-bold text-white">{{ $totalFree }}</p>
            </div>
            <div class="bg-emerald-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Unpaid</h3>
                <p class="text-4xl font-bold text-white">{{ $totalUnpaid }}</p>
            </div>
            <div class="bg-emerald-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Refunded</h3>
                <p class="text-4xl font-bold text-white">{{ $totalRefunded }}</p>
            </div>

            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Confirmed</h3>
                <p class="text-4xl font-bold text-white">{{ $totalConfirmed }}</p>
            </div>
            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Pending</h3>
                <p class="text-4xl font-bold text-white">{{ $totalPending }}</p>
            </div>
            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Dropped Out</h3>
                <p class="text-4xl font-bold text-white">{{ $totalDroppedOut }}</p>
            </div>
            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Cancelled</h3>
                <p class="text-4xl font-bold text-white">{{ $totalCancelled }}</p>
            </div>
        </div>




        <div class="grid grid-cols-5 gap-4 text-center mt-10">
            <div class="bg-indigo-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Online</h3>
                <p class="text-4xl font-bold text-white">{{ $totalOnline }}</p>
            </div>
            <div class="bg-indigo-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Imported</h3>
                <p class="text-4xl font-bold text-white">{{ $totalImported }}</p>
            </div>
            <div class="bg-indigo-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Onsite</h3>
                <p class="text-4xl font-bold text-white">{{ $totalOnsite }}</p>
            </div>
            <div class="bg-indigo-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Credit Card</h3>
                <p class="text-4xl font-bold text-white">{{ $totalCreditCard }}</p>
            </div>
            <div class="bg-indigo-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Bank Transfer</h3>
                <p class="text-4xl font-bold text-white">{{ $totalBankTransfer }}</p>
            </div>
        </div>


        <div class="grid grid-cols-3 gap-8 mt-10">
            @if (count($arrayCountryTotal) > 0)
                <div class="bg-white rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold bg-blue-700 text-white px-6 py-4 rounded-t-lg">Country</h2>
                    <div class="grid grid-cols-2 bg-blue-500">
                        <p class="py-3 px-6 font-medium text-sm text-white">Name</p>
                        <p class="py-3 px-6 font-medium text-sm text-white">Total</p>
                    </div>
                    @foreach ($arrayCountryTotal as $country)
                        <div class="grid grid-cols-2">
                            <p class="py-4 px-6 border-b">{{ $country['name'] }}</p>
                            <p class="py-4 px-6 border-b">{{ $country['total'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif


            @if (count($arrayCompanyTotal) > 0)
                <div class="bg-white rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold bg-blue-700 text-white px-6 py-4 rounded-t-lg">Company</h2>
                    <div class="grid grid-cols-2 bg-blue-500">
                        <p class="py-3 px-6 font-medium text-sm text-white">Name</p>
                        <p class="py-3 px-6 font-medium text-sm text-white">Total</p>
                    </div>
                    @foreach ($arrayCompanyTotal as $company)
                        <div class="grid grid-cols-2">
                            <p class="py-4 px-6 border-b">{{ $company['name'] }}</p>
                            <p class="py-4 px-6 border-b">{{ $company['total'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif


            @if (count($arrayRegistrationTypeTotal) > 0)
                <div class="bg-white rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold bg-blue-700 text-white px-6 py-4 rounded-t-lg">Registration Type</h2>
                    <div class="grid grid-cols-2 bg-blue-500">
                        <p class="py-3 px-6 font-medium text-sm text-white">Name</p>
                        <p class="py-3 px-6 font-medium text-sm text-white">Total</p>
                    </div>
                    @foreach ($arrayRegistrationTypeTotal as $registrationType)
                        <div class="grid grid-cols-2">
                            <p class="py-4 px-6 border-b">{{ $registrationType['name'] }}</p>
                            <p class="py-4 px-6 border-b">{{ $registrationType['total'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
