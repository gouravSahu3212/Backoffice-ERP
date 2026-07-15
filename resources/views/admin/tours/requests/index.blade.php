@extends('layouts.dashboard')

@section('page-title', 'Tour Bookings')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-3 border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                <div>
                                    <h1 class="h3 mb-2">Tour Requests</h1>
                                    <p class="text-muted mb-0">Enquiries submitted by agents from the tours module.</p>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive border rounded-3">
                            <table class="table mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Request ID</th>
                                        <th scope="col">Tour</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Passport</th>
                                        <th scope="col">Departure</th>
                                        <th scope="col">Pax</th>
                                        <th scope="col">Agent</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">No tour requests yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection