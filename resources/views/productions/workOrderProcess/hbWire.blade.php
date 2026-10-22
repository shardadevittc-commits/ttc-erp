<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Work Order</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  .text-size { font-size: 14px; }
  .fheading { font-weight: 600; border-bottom: 1px solid #dee2e6; padding-bottom: 4px; }
  .accordion-button.done { background-color: #28a745 !important; color: #fff; }
</style>
</head>
<body class="bg-light">

<!-- Header -->
<nav class="navbar navbar-expand bg-white border-bottom px-3 no-print">
  <a href="index.html" class="navbar-brand">
    <img src="https://ttcrobotronics.com/assets/images/ttc-robotronics-logo.png" alt="Logo" height="40">
  </a>
  <div class="ms-auto d-flex align-items-center gap-3">
    <span role="button" id="fullscreen"><i class="bi bi-fullscreen"></i></span>
    <button type="button" class="btn btn-link p-0 position-relative" data-bs-toggle="dropdown">
      <i class="bi bi-bell text-danger fs-5"></i>
      <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">0</span>
    </button>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
        <img src="assets/images/profiles/profile-pic.png" alt="" width="32" height="32" class="rounded-circle me-2">
        <span>Admin</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> My Profile</a></li>
        <li><a class="dropdown-item" href="change_password.html"><i class="bi bi-key"></i> Change Password</a></li>
        <li><a class="dropdown-item" href="logout.html"><i class="bi bi-power"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid py-3">

  <!-- Page header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Work Order</h1>
    <a href="work_order_history.html" class="btn btn-outline-secondary btn-sm">History</a>
  </div>

  

  <!-- ============ OPERATION 1 ============ -->
  <div class="accordion accordion-flush mb-3" id="accordionOp1">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#work1" aria-expanded="true" aria-controls="work1">
          <b>ACID PICKLING | PHOSPHATING (Operation 1)</b>
        </button>
      </h2>
      <div id="work1" class="accordion-collapse collapse show" data-bs-parent="#accordionOp1">
        <div class="accordion-body">
          <form method="post">
            <input type="hidden" name="workStatus" value="operation1">
            <input type="hidden" name="doAction" value="Addoprt">
            <input type="hidden" name="oprtion1" value="Operation 1">
            <input type="hidden" name="processtype" value="acid_pickling_phosphating">
            <div class="row">
              <div class="col-md-12 mb-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="acidPrcklingoptionacid" id="acid" value="optionacid" checked>
                  <label class="form-check-label fs-6" for="acid">ACID Pickling</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="acidPrcklingoptionphos" id="phos" value="optionphos">
                  <label class="form-check-label fs-6" for="phos">For Phosphating</label>
                </div>
              </div>
              <div class="col-md-12 mb-3">
                <label class="col-form-label">Remarks</label>
                <textarea class="form-control" name="acidPrcklingRemarks"></textarea>
              </div>
              <div class="col-12 text-end">
                <input type="submit" class="btn btn-primary">
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ OPERATION 2 ============ -->
  <div class="accordion accordion-flush mb-3" id="accordionOp2">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#work2" aria-expanded="true" aria-controls="work2">
          <b>WIRE DRAWING (Operation 2)</b>
        </button>
      </h2>
      <div id="work2" class="accordion-collapse collapse show" data-bs-parent="#accordionOp2">
        <div class="accordion-body">
          <form method="post">
            <input type="hidden" name="workStatus" value="operation2">
            <input type="hidden" name="doAction" value="Addoprt">
            <input type="hidden" name="oprtion2" value="Operation 2">
            <div class="row">
              <div class="col-md-3">
                <label class="col-form-label">FS Bundle Weight (From SO)</label>
                <div class="input-group">
                  <input type="number" step="0.001" id="fsbndle" class="form-control" name="fsbndle" value="" readonly>
                  <select class="form-select" name="fsbndleUnit" style="max-width:100px;">
                    <option value="KG" selected>KG</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <label class="col-form-label">Bundle</label>
                <input type="number" class="form-control" id="pcsfsbndle" name="pcsfsbndle" placeholder="Enter Final bundle Count" required>
              </div>
              <div class="col-md-3">
                <label class="col-form-label text-wrap text-size">Bundles * Weight = Final Prop Weight</label>
                <input type="text" class="form-control" id="fullBundleCount" value="0 * 0 = 0" disabled>
              </div>
              <div class="col-md-4">
                <label class="col-form-label text-size">Bundles * loose Weight = Final loose Weight</label>
                <input type="text" class="form-control" value="1 * 0 = 0" disabled>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mt-3">
                <div class="fheading">Raw Material</div>
                <div class="row">
                  <div class="col-md-6 mt-3">
                    <label class="col-form-label">RM Size (Purchase)</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name="rmSize" readonly>
                      <select class="form-select" name="rmSizeUnit" style="max-width:100px;">
                        <option value="MM">MM</option>
                        <option value="THOU">THOU</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 mt-3">
                    <label class="col-form-label">Observed Size</label>
                    <div class="input-group mb-3">
                      <input type="text" name="rmobservedSize" class="form-control">
                      <select class="form-select" name="rmobservedSizeUnit" style="max-width:100px;">
                        <option value="MM">MM</option>
                        <option value="THOU">THOU</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6 mt-3">
                <div class="fheading">Finished Material</div>
                <div class="row">
                  <div class="col-md-6 mt-3">
                    <label class="col-form-label">FM Size (Sale)</label>
                    <div class="input-group mb-3">
                      <input type="text" name="fsSize" class="form-control" readonly>
                      <select class="form-select" name="fsSizeUnit" style="max-width:100px;">
                        <option value="MM">MM</option>
                        <option value="THOU">THOU</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 mt-3">
                    <label class="col-form-label">Observed Size</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name="fsobservedSize">
                      <select class="form-select" name="fsObservedSizeUnit" style="max-width:100px;">
                        <option value="MM">MM</option>
                        <option value="THOU">THOU</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 text-end mb-4">
                <input type="submit" class="btn btn-primary">
              </div>
            </div>

            <hr>
            <!-- Finish Material Info (HB wire) -->
            <div class="row bg-light mt-4 p-2">
              <div class="col-md-12 mt-3">
                <div class="fheading">Finish Material Info</div>
                <div class="row">
                  <div class="col-md-2 mt-3">
                    <label class="col-form-label text-size">Send RM Qty (Kg.)</label>
                    <input type="text" class="form-control" value="10000" disabled>
                  </div>
                  <div class="col-md-2 mt-3">
                    <label class="col-form-label text-size">Non. Reco. Scrap Qty (Kg.)</label>
                    <input type="text" class="form-control" value="0" disabled>
                  </div>
                  <div class="col-md-2 mt-3">
                    <label class="col-form-label text-size">Misc. Scrap Qty (Kg.)</label>
                    <input type="number" step="0.001" min="0" id="reco_scrap" class="form-control">
                  </div>
                  <div class="col-md-3 mt-3">
                    <label class="col-form-label text-size">Finish Qty (Kg.)</label>
                    <input type="text" id="finish_qty" class="form-control" value="0.00" disabled>
                  </div>
                  <div class="col-md-3 mt-3">
                    <label class="col-form-label text-size">Bundle Qty</label>
                    <input type="text" class="form-control" value="0" disabled>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ OPERATION 3 ============ -->
  <div class="accordion accordion-flush mb-3" id="accordionOp3">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#work3" aria-expanded="true" aria-controls="work3">
          <b>HEAT TREATMENT (Operation 3)</b>
        </button>
      </h2>
      <div id="work3" class="accordion-collapse collapse show" data-bs-parent="#accordionOp3">
        <div class="accordion-body">
          <form method="post">
            <input type="hidden" name="workStatus" value="operation3">
            <input type="hidden" name="doAction" value="Addoprt">
            <input type="hidden" name="oprtion3" value="Operation 3">
            <div class="row">
              <div class="col-md-3 mt-3">
                <label class="col-form-label">Size First Draw</label>
                <input class="form-control" type="text" name="heatSizeFstDraw" readonly>
              </div>
              <div class="col-md-3 mt-3">
                <label class="col-form-label">No of Bundles</label>
                <input class="form-control" type="text" name="bundlecnt" readonly>
              </div>
              <div class="col-md-3 mt-3">
                <label class="col-form-label">Batch No.</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="batchNo" id="batchNo" aria-label="Batch Number">
                  <button class="btn btn-secondary" type="button" id="generateBtn">Generate</button>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3 mt-3">
                <label class="col-form-label">FM Size (Sale Order)</label>
                <input type="text" class="form-control" name="heatfsSize" readonly>
              </div>
              <div class="col-md-3 mt-3">
                <label class="col-form-label">Hardness (Sale Order)</label>
                <div class="input-group mb-3">
                  <input class="form-control" type="text" name="htfsSizeHardness">
                  <select class="form-select" name="heatfsSizeHardnessUnit" style="max-width:100px;">
                    <option value="BHN">BHN</option>
                    <option value="HRC">HRC</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3 mt-3">
                <label class="col-form-label">Hardness Observed Value (SO)</label>
                <div class="input-group mb-3">
                  <input class="form-control" type="text" name="ObsdhtfsSHrdns">
                  <select class="form-select" name="ObsdhtfsSHrdnsUnit" style="max-width:100px;">
                    <option value="BHN">BHN</option>
                    <option value="HRC">HRC</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3 mt-3">
                <label class="col-form-label">Grade</label>
                <input class="form-control" type="text" name="ttrtGrade" readonly>
              </div>
              <div class="col-12 text-end mt-3">
                <input type="submit" class="btn btn-primary">
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ OPERATION 4 ============ -->
  <div class="accordion accordion-flush mb-3" id="accordionOp4">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#work4" aria-expanded="true" aria-controls="work4">
          <b>ACID PICKLING | PHOSPHATING (Operation 4)</b>
        </button>
      </h2>
      <div id="work4" class="accordion-collapse collapse show" data-bs-parent="#accordionOp4">
        <div class="accordion-body">
          <form method="post">
            <input type="hidden" name="workStatus" value="operation4">
            <input type="hidden" name="doAction" value="Addoprt">
            <input type="hidden" name="oprtion4" value="Operation 4">
            <input type="hidden" name="processtype" value="acid_pickling_phosphating">
            <div class="row">
              <div class="col-md-12">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="acid" id="acid4" value="optionacid">
                  <label class="form-check-label fs-6" for="acid4">ACID Pickling</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="phos" id="phos4" value="optionphos">
                  <label class="form-check-label fs-6" for="phos4">For Phosphating</label>
                </div>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label">Batch No.</label>
                <input class="form-control" type="text" name="batchNoOld">
              </div>
              <div class="col-md-8 mb-3">
                <label class="col-form-label">Remarks</label>
                <textarea class="form-control" name="opfrthrmarks"></textarea>
              </div>
              <div class="col-12 text-end">
                <input type="submit" class="btn btn-primary">
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ OPERATION 5 (Final Pass) ============ -->
  <div class="accordion accordion-flush mb-3" id="accordionOp5">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#work5" aria-expanded="true" aria-controls="work5">
          <b>WIRE DRAWING (Final Pass)</b>
        </button>
      </h2>
      <div id="work5" class="accordion-collapse collapse show" data-bs-parent="#accordionOp5">
        <div class="accordion-body">
          <form method="post">
            <input type="hidden" name="workStatus" value="operation5">
            <input type="hidden" name="doAction" value="Addoprt">
            <input type="hidden" name="oprtion5" value="Operation 5">
            <div class="row">
              <div class="col-md-3">
                <label class="col-form-label">Finished Bundle Weight (From SO)</label>
                <div class="input-group">
                  <input type="number" step="0.001" class="form-control" name="wrdfPassfbdle" readonly>
                  <select class="form-select" name="wrdfPassfbdleunt" style="max-width:100px;">
                    <option value="KG">KG</option>
                    <option value="MT">TONS</option>
                  </select>
                </div>
              </div>
              <div class="col-md-5">
                <label class="col-form-label">Other Misc. Bundle weight (Kgs)</label>
                <span data-bs-toggle="tooltip" title="Weight entered here will be deducted from the main bundle weight and added to Misc Scrap." style="cursor:pointer;">ⓘ</span>
                <input type="number" step="0.001" class="form-control" name="misc_bundle_weight" placeholder="Enter Misc. Bundle weight in Kg EX: 250">
              </div>
              <div class="col-md-4">
                <label class="col-form-label">Misc. (Kgs)</label>
                <span data-bs-toggle="tooltip" title="Weight entered here will be deducted from the main bundle weight and added to Misc Scrap." style="cursor:pointer;">ⓘ</span>
                <input type="number" step="0.001" class="form-control" name="manage_misc_scrap" id="manage_misc_scrap" placeholder="Enter Misc. Bundle weight in Kg EX: 250">
              </div>

              <div class="col-md-6 mt-3">
                <div class="row">
                  <div class="col-md-6 mt-3">
                    <label class="col-form-label">Size first Draw</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name="opfthsfd" readonly>
                      <select class="form-select" name="opfthsfdUnit" style="max-width:100px;">
                        <option value="MM">MM</option>
                        <option value="THOU">THOU</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 mt-3">
                    <label class="col-form-label">Observed Size</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name="obopfthsfd">
                      <select class="form-select" name="obopfthsfdunt" style="max-width:100px;">
                        <option value="MM">MM</option>
                        <option value="THOU">THOU</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6 mt-3">
                <div class="row">
                  <div class="col-md-6 mt-3">
                    <label class="col-form-label">FM Size (Sale)</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name="optfsize" readonly>
                      <select class="form-select" name="optfsizeUnit" style="max-width:100px;">
                        <option value="MM">MM</option>
                        <option value="THOU">THOU</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 mt-3">
                    <label class="col-form-label">Observed Size</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name="oboptfsize">
                      <select class="form-select" name="oboptfsizeUnt" style="max-width:100px;">
                        <option value="MM">MM</option>
                        <option value="THOU">THOU</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 text-end">
                <input type="submit" class="btn btn-primary" name="submit" value="Submit and Generate Token">
              </div>
            </div>
          </form>

          <hr>
          <div class="row bg-light mt-4 p-2">
            <div class="col-md-12 mt-3">
              <div class="fheading">Finish Material Info</div>
              <div class="row">
                <div class="col-md-2 mt-2">
                  <label class="col-form-label">Send RM Qty (Kg.)</label>
                  <input type="text" class="form-control" value="10000" disabled>
                </div>
                <div class="col-md-3 mt-3">
                  <label class="col-form-label text-size">Non. Reco. Scrap Qty (Kg.)</label>
                  <input type="text" class="form-control" value="0" disabled>
                </div>
                <div class="col-md-2 mt-2">
                  <label class="col-form-label text-size">Reco. Scrap Qty (Kg.)</label>
                  <input type="text" class="form-control" value="0" disabled>
                </div>
                <div class="col-md-2 mt-2">
                  <label class="col-form-label">Finish Qty (Kg.)</label>
                  <input type="text" class="form-control" value="0" disabled>
                </div>
                <div class="col-md-3 mt-3">
                  <label class="col-form-label">Bundle Qty</label>
                  <input type="text" class="form-control" value="0" disabled>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ GENERATED TOKEN LISTING ============ -->
  <div class="accordion accordion-flush mb-5">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button done" type="button" data-bs-toggle="collapse" data-bs-target="#tokenList" aria-expanded="true" aria-controls="tokenList">
          <b>GENERATED TOKEN LISTING</b>
        </button>
      </h2>
      <div id="tokenList" class="accordion-collapse collapse show">
        <div class="accordion-body">
          <!-- generate_stickers.php ka content yahan aayega -->
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Batch No. generate
  document.getElementById('generateBtn').addEventListener('click', function () {
    var digit = Math.floor(Math.random() * 10);
    document.getElementById('batchNo').value = 'DBS1-' + digit;
  });
  // Fullscreen toggle
  document.getElementById('fullscreen').addEventListener('click', function () {
    if (!document.fullscreenElement) document.documentElement.requestFullscreen();
    else document.exitFullscreen();
  });
  // Tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) { new bootstrap.Tooltip(el); });
</script>
</body>
</html>
