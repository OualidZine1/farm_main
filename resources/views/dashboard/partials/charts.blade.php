<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Stock Value Over Time</h6>
                <div class="dropdown no-arrow">
                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="chartPeriod" 
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Last 30 Days
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="chartPeriod">
                        <a class="dropdown-item" href="#" data-period="7">Last 7 Days</a>
                        <a class="dropdown-item" href="#" data-period="30">Last 30 Days</a>
                        <a class="dropdown-item" href="#" data-period="90">Last 90 Days</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="stockChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Stock Distribution by Category</h6>
            </div>
            <div class="card-body">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>