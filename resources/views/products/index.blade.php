<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Product Inventory</h1>

    <!-- Form Section -->
    <form id="product-form" class="mt-4">
        <input type="hidden" id="edit-index" name="index">
        <div class="card shadow-sm border-0">
            <div class="card-header text-white" style="background-color: rgb(58, 136, 251) !important">
                <h5 class="mb-0">Add Product</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Product Name -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Product Name" required>
                            <label for="product_name">Product Name</label>
                        </div>
                    </div>
                    <!-- Quantity in Stock -->
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock" placeholder="Quantity in Stock" required>
                            <label for="quantity_in_stock">Quantity in Stock</label>
                        </div>
                    </div>
                    <!-- Price Per Item -->
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="number" step="0.01" class="form-control" id="price_per_item" name="price_per_item" placeholder="Price per Item" required>
                            <label for="price_per_item">Price per Item</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn text-white btn-lg px-4" style="background-color: rgb(58, 136, 251) !important">Submit</button>
            </div>
        </div>
    </form>


    <!-- Data Display Section -->
    <h2 class="mt-5">Products</h2>
    <table class="table table-bordered mt-3" id="products-table">
        <thead>
        <tr>
            <th>Product Name</th>
            <th>Quantity in Stock</th>
            <th>Price per Item</th>
            <th>Date Time Submitted</th>
            <th>Total Value</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
        <tr>
            <td colspan="4" class="text-end"><strong>Total:</strong></td>
            <td id="total-sum"></td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        // CSRF Token
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Fetch and display data
        function loadProducts() {
            $.get('{{ route('products.fetch') }}', function (data) {
                const tableBody = $('#products-table tbody');
                tableBody.empty();

                data.products.forEach((product, index) => {
                    const totalValue = product.quantity_in_stock * product.price_per_item;
                    tableBody.append(`
                        <tr>
                            <td>${product.product_name}</td>
                            <td>${product.quantity_in_stock}</td>
                            <td>${product.price_per_item}</td>
                            <td>${product.datetime_submitted}</td>
                            <td>${totalValue.toFixed(2)}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-index="${index}">Edit</button>
                            </td>
                        </tr>
                    `);
                });

                $('#total-sum').text(data.totalSum.toFixed(2));
            });
        }

        loadProducts();

        // Submit form via AJAX
        $('#product-form').on('submit', function (e) {
            e.preventDefault();

            const index = $('#edit-index').val();
            console.log(index);
            const url = index
                ? `{{ url('/update') }}/${index}` // Update URL
                : '{{ route('products.store') }}'; // Create URL

            const method = index ? 'POST' : 'POST'; // Both are POST

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function (response) {
                    $('#product-form')[0].reset();
                    $('#edit-index').val('');
                    loadProducts();
                }
            });
        });

        // Edit button click
        $(document).on('click', '.edit-btn', function () {
            const index = $(this).data('index');
            $.get(`{{ url('/edit') }}/${index}`, function (response) {
                const product = response.product;
                // Populate the form with existing data
                $('#edit-index').val(index);
                $('#product_name').val(product.product_name);
                $('#quantity_in_stock').val(product.quantity_in_stock);
                $('#price_per_item').val(product.price_per_item);
            });
        });
    });
</script>
</body>
</html>
