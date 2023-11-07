@extends('admin.layouts.master')

@section('content')
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>

    <div class="container mx-auto my-10">

        <div class="grid grid-cols-1 gap-4 text-center">
            <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Confirmed Visitors</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['totalConfirmedVisitors'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-center mt-10">
            <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Total registration</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['totalVisitors'] }}</p>
            </div>
            <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Registered today</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['totalRegisteredToday'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 text-center mt-10">
            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Visitor badge printed</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['visitorBadgePrinted'] }}</p>
            </div>
            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Total prints</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['totalBadgePrinted'] }}</p>
            </div>
            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Duplicated badge printed</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['duplicateBadgePrinted'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 text-center mt-10">
            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Visitor badge scanned</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['visitorBadgeScanned'] }}</p>
            </div>
            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Total scanned</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['totalBadgeScanned'] }}</p>
            </div>
            <div class="bg-teal-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Duplicated badge scanned</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['duplicateBadgeScanned'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 text-center mt-10">
            <div class="bg-green-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Total amount paid</h3>
                <p class="text-4xl font-bold text-white">$ {{ number_format($finalData['totalAmountPaid'], 2, '.', ',') }}</p>
            </div>
            <div class="bg-green-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Paid today</h3>
                <p class="text-4xl font-bold text-white">{{ $finalData['totalPaidToday'] }}</p>
            </div>
            <div class="bg-green-500 p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-white">Revenue today</h3>
                <p class="text-4xl font-bold text-white">$ {{ number_format($finalData['totalAmountPaidToday'], 2, '.', ',') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5 mt-10">
            <div class="border border-gray-200">
                <p class="py-2 bg-registrationPrimaryColor text-xl text-white text-center">Pass Type</p>
                <div class="p-5">
                    <canvas id="passType"></canvas>
                </div>
            </div>

            <div class="border border-gray-200">
                <p class="py-2 bg-registrationPrimaryColor text-xl text-white text-center">Payment Status</p>
                <div class="p-5">
                    <canvas id="paymentStatus"></canvas>
                </div>
            </div>

            <div class="border border-gray-200">
                <p class="py-2 bg-registrationPrimaryColor text-xl text-white text-center">Registration Status</p>
                <div class="p-5">
                    <canvas id="registrationStatus"></canvas>
                </div>
            </div>
        </div>

        <div class="flex justify-center items-center">

            <div class="grid grid-cols-2 gap-5 mt-10">
                <div class="border border-gray-200">
                    <p class="py-2 bg-registrationPrimaryColor text-xl text-white text-center">Registration Method</p>
                    <div class="p-5">
                        <canvas id="registrationMethod"></canvas>
                    </div>
                </div>
    
                <div class="border border-gray-200">
                    <p class="py-2 bg-registrationPrimaryColor text-xl text-white text-center">Payment Method</p>
                    <div class="p-5">
                        <canvas id="paymentMethod"></canvas>
                    </div>
                </div>
            </div>
        </div>




        <div class="grid grid-cols-3 gap-8 mt-10">
            @if (count($finalData['arrayCountryTotal']) > 0)
                <div class="bg-white rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold bg-blue-700 text-white px-6 py-4 rounded-t-lg">Country</h2>
                    <div class="grid grid-cols-2 justify-center items-center bg-blue-500">
                        <p class="py-3 px-6 font-medium text-sm text-white">Name</p>
                        <p class="py-3 px-6 font-medium text-sm text-white">Total</p>
                    </div>
                    @foreach ($finalData['arrayCountryTotal'] as $country)
                        <div class="grid grid-cols-2 justify-center items-center">
                            <p class="py-4 px-6 border-b">{{ $country['name'] }}</p>
                            <p class="py-4 px-6 border-b">{{ $country['total'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif


            @if (count($finalData['arrayCompanyTotal']) > 0)
                <div class="bg-white rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold bg-blue-700 text-white px-6 py-4 rounded-t-lg">Company</h2>
                    <div class="grid grid-cols-2 justify-center items-center bg-blue-500">
                        <p class="py-3 px-6 font-medium text-sm text-white">Name</p>
                        <p class="py-3 px-6 font-medium text-sm text-white">Total</p>
                    </div>
                    @foreach ($finalData['arrayCompanyTotal'] as $company)
                        <div class="grid grid-cols-2 justify-center items-center">
                            <p class="py-4 px-6 border-b">{{ $company['name'] }}</p>
                            <p class="py-4 px-6 border-b">{{ $company['total'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif


            @if (count($finalData['arrayRegistrationTypeTotal']) > 0)
                <div class="bg-white rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold bg-blue-700 text-white px-6 py-4 rounded-t-lg">Registration Type</h2>
                    <div class="grid grid-cols-2 justify-center items-center bg-blue-500">
                        <p class="py-3 px-6 font-medium text-sm text-white">Name</p>
                        <p class="py-3 px-6 font-medium text-sm text-white">Total</p>
                    </div>
                    @foreach ($finalData['arrayRegistrationTypeTotal'] as $registrationType)
                        <div class="grid grid-cols-2 justify-center items-center">
                            <p class="py-4 px-6 border-b">{{ $registrationType['name'] }}</p>
                            <p class="py-4 px-6 border-b">{{ $registrationType['total'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const passType = document.getElementById('passType');
        const paymentStatus = document.getElementById('paymentStatus');
        const registrationStatus = document.getElementById('registrationStatus');
        const registrationMethod = document.getElementById('registrationMethod');
        const paymentMethod = document.getElementById('paymentMethod');

        new Chart(passType, {
            type: 'doughnut',
            data: {
                labels: [
                    'Full Member',
                    'Member',
                    'Non-Member',
                ],
                datasets: [{
                    label: 'Pass Type',
                    data: @json($finalData['passType']),
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                    ],
                    hoverOffset: 4
                }]
            },
        });

        new Chart(paymentStatus, {
            type: 'doughnut',
            data: {
                labels: [
                    'Paid',
                    'Free',
                    'Unpaid',
                    'Refunded',
                ],
                datasets: [{
                    label: 'Payment Status',
                    data: @json($finalData['paymentStatus']),
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(255, 105, 86)',
                    ],
                    hoverOffset: 4
                }]
            },
        });

        new Chart(registrationStatus, {
            type: 'doughnut',
            data: {
                labels: [
                    'Confirmed',
                    'Pending',
                    'Dropped Out',
                    'Cancelled',
                ],
                datasets: [{
                    label: 'Registration Status',
                    data: @json($finalData['registrationStatus']),
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(255, 105, 86)',
                    ],
                    hoverOffset: 4
                }]
            },
        });

        new Chart(registrationMethod, {
            type: 'doughnut',
            data: {
                labels: [
                    'Online',
                    'Imported',
                    'Onsite',
                ],
                datasets: [{
                    label: 'Registration Method',
                    data: @json($finalData['registrationMethod']),
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                    ],
                    hoverOffset: 4
                }]
            },
        });

        new Chart(paymentMethod, {
            type: 'doughnut',
            data: {
                labels: [
                    'Credit Card',
                    'Bank Transfer',
                ],
                datasets: [{
                    label: 'Payment Method',
                    data: @json($finalData['paymentMethod']),
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                    ],
                    hoverOffset: 4
                }]
            },
        });
    </script>
@endsection
