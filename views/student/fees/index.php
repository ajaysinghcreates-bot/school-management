<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees - Student Portal</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/student/dashboard">Student Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/student/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/results">Results</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/student/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/profile">Profile</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">Fee Payment History</h1>

        <!-- Fee Summary -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Total Fees</h5>
                        <h3 id="total-fees">$0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Paid</h5>
                        <h3 id="paid-fees">$0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5>Outstanding</h5>
                        <h3 id="outstanding-fees">$0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fees Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Fee Details</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="fees-table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Paid Amount</th>
                                        <th>Payment Date</th>
                                        <th>Receipt</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="fees-body">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadFees();
        });

        function loadFees() {
            fetch('/student/api/fees')
                .then(response => response.json())
                .then(data => {
                    // Update summary
                    let total = 0, paid = 0;
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(fee => {
                            total += parseFloat(fee.amount || 0);
                            paid += parseFloat(fee.paid_amount || 0);
                        });
                    }
                    document.getElementById('total-fees').textContent = '$' + total.toFixed(2);
                    document.getElementById('paid-fees').textContent = '$' + paid.toFixed(2);
                    document.getElementById('outstanding-fees').textContent = '$' + (total - paid).toFixed(2);

                    // Update table
                    const tbody = document.getElementById('fees-body');
                    if (data.data && data.data.length > 0) {
                        const rows = data.data.map(fee => `
                            <tr>
                                <td>${fee.description || 'Fee'}</td>
                                <td>$${parseFloat(fee.amount || 0).toFixed(2)}</td>
                                <td>${fee.due_date ? new Date(fee.due_date).toLocaleDateString() : '-'}</td>
                                <td>$${parseFloat(fee.paid_amount || 0).toFixed(2)}</td>
                                <td>${fee.payment_date ? new Date(fee.payment_date).toLocaleDateString() : '-'}</td>
                                <td>${fee.receipt_number || '-'}</td>
                                <td>
                                    <span class="badge ${parseFloat(fee.paid_amount || 0) >= parseFloat(fee.amount || 0) ? 'bg-success' : 'bg-danger'}">
                                        ${parseFloat(fee.paid_amount || 0) >= parseFloat(fee.amount || 0) ? 'Paid' : 'Pending'}
                                    </span>
                                </td>
                            </tr>
                        `).join('');
                        tbody.innerHTML = rows;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No fee records found</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading fees:', error);
                    document.getElementById('fees-body').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
                });
        }
    </script>
</body>
</html>