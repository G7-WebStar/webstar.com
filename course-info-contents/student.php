<?php
?>
<div class="container-fluid">
    <div class="row align-items-center justify-content-center flex-column flex-md-row">
        <div class="col-8 col-sm-6 col-md-12 col-lg-6 d-flex search-container mb-2 mb-lg-0">
            <input type="text" placeholder="Search classmates" class="form-control py-1 text-reg text-lg-12 text-14">
            <button type="button" class="btn-outline-secondary">
                <i class="bi bi-search me-2"></i>
            </button>
        </div>
        <div class="col-6 d-flex justify-content-center justify-content-lg-start align-items-center">
            <span class="dropdown-label me-2">View by:</span>
            <form method="POST">
                <select class="dropdown-custom" name="dateFilter" onchange="this.form.submit()">
                    <option value="Monthly" <?php echo $dateFilter == 'Monthly' ? 'selected' : '';?> class="dropdown-item text-reg">Monthly</option>
                    <option value="Weekly" <?php echo ($dateFilter == 'Weekly' || empty($dateFilter)) ? 'selected' : '';?> class="dropdown-item text-reg">Weekly</option>
                    <option value="Daily" <?php echo $dateFilter == 'Daily' ? 'selected' : '';?> class="dropdown-item text-reg">Daily</option>
                </select>
            </form>
        </div>
    </div>
</div>
