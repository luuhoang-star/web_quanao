{{-- Component: ATINO Smart Fit Finder & Size Advisor Modal --}}
@php
    $categoryName = mb_strtolower($product->category->name ?? '');
    $isPants = str_contains($categoryName, 'quần') || 
               str_contains($categoryName, 'jeans') || 
               str_contains($categoryName, 'pant') || 
               str_contains($categoryName, 'short') || 
               str_contains($categoryName, 'tây');

    $hasNumberSizes = false;
    $hasLetterSizes = false;
    $availableSizesList = [];

    if(isset($sizes) && $sizes->isNotEmpty()) {
        foreach($sizes as $sz) {
            $szName = trim((string)$sz->name);
            $availableSizesList[] = $szName;
            if (is_numeric($szName)) {
                $hasNumberSizes = true;
            } else {
                $hasLetterSizes = true;
            }
        }
    }

    $defaultProductType = ($isPants || ($hasNumberSizes && !$hasLetterSizes)) ? 'bottoms' : 'tops';
@endphp

<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true" data-default-type="{{ $defaultProductType }}">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <!-- Header Modal -->
            <div class="modal-header bg-dark text-white py-3 px-4 position-relative border-bottom border-dark-subtle">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-warning bg-opacity-20 d-flex align-items-center justify-content-center p-2" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-ruler-combined text-warning fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white fs-6 d-flex align-items-center gap-2" id="sizeGuideModalLabel">
                            <span>ATINO SMART FIT FINDER</span>
                            <span class="badge bg-warning text-dark extra-small fw-bold px-2 py-0.5 rounded-pill">AI Advisor</span>
                        </h5>
                        <small class="text-white-50 extra-small">Hệ thống gợi ý kích cỡ trang phục thông minh theo số đo thực tế</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Tab Navigation Bar -->
            <div class="bg-light px-4 pt-3 border-bottom">
                <ul class="nav nav-pills nav-fill gap-2" id="sizeGuideTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold small py-2 px-3 rounded-3" id="tab-calculator-tab" data-bs-toggle="pill" data-bs-target="#tab-calculator" type="button" role="tab" aria-controls="tab-calculator" aria-selected="true">
                            <i class="fa-solid fa-wand-magic-sparkles me-1.5 text-warning"></i> Gợi Ý Size Nhanh
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold small py-2 px-3 rounded-3" id="tab-chart-tab" data-bs-toggle="pill" data-bs-target="#tab-chart" type="button" role="tab" aria-controls="tab-chart" aria-selected="false">
                            <i class="fa-solid fa-table-list me-1.5 text-primary"></i> Bảng Thông Số Chi Tiết
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold small py-2 px-3 rounded-3" id="tab-guide-tab" data-bs-toggle="pill" data-bs-target="#tab-guide" type="button" role="tab" aria-controls="tab-guide" aria-selected="false">
                            <i class="fa-solid fa-tape me-1.5 text-info"></i> Cách Đo Tại Nhà
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Modal Body Content -->
            <div class="modal-body p-4">
                <div class="tab-content" id="sizeGuideTabsContent">
                    
                    <!-- ================= TAB 1: MÁY TÍNH GỢI Ý SIZE THÔNG MINH ================= -->
                    <div class="tab-pane fade show active" id="tab-calculator" role="tabpanel" aria-labelledby="tab-calculator-tab">
                        <div class="row g-4">
                            <!-- Cột nhập liệu số đo & form dáng -->
                            <div class="col-lg-7">
                                <!-- Chọn loại trang phục -->
                                <div class="mb-3.5">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-2">1. Loại trang phục đang chọn:</label>
                                    <div class="btn-group w-100 p-1 bg-light rounded-3 border" role="group" id="fit-apparel-type-group">
                                        <input type="radio" class="btn-check" name="fit_apparel_type" id="fit_type_tops" value="tops" {{ $defaultProductType === 'tops' ? 'checked' : '' }}>
                                        <label class="btn btn-sm rounded-2 border-0 fw-bold py-1.5" for="fit_type_tops">
                                            <i class="fa-solid fa-shirt me-1"></i> Áo (Tops: S - XXL)
                                        </label>

                                        <input type="radio" class="btn-check" name="fit_apparel_type" id="fit_type_bottoms" value="bottoms" {{ $defaultProductType === 'bottoms' ? 'checked' : '' }}>
                                        <label class="btn btn-sm rounded-2 border-0 fw-bold py-1.5" for="fit_type_bottoms">
                                            <i class="fa-solid fa-vest-patches me-1"></i> Quần (Bottoms: 28 - 34)
                                        </label>
                                    </div>
                                </div>

                                <!-- Slider Chiều Cao -->
                                <div class="mb-3.5">
                                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-0">2. Chiều cao của bạn:</label>
                                        <span class="badge bg-dark px-2.5 py-1 fs-6 font-monospace fw-bold text-white rounded-pill">
                                            <span id="display-height-val">170</span> cm
                                        </span>
                                    </div>
                                    <input type="range" class="form-range" id="fit-range-height" min="145" max="200" step="1" value="170">
                                    <div class="d-flex justify-content-between text-muted extra-small">
                                        <span>145 cm</span>
                                        <span>170 cm</span>
                                        <span>200 cm</span>
                                    </div>
                                </div>

                                <!-- Slider Cân Nặng -->
                                <div class="mb-3.5">
                                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-0">3. Cân nặng của bạn:</label>
                                        <span class="badge bg-dark px-2.5 py-1 fs-6 font-monospace fw-bold text-white rounded-pill">
                                            <span id="display-weight-val">65</span> kg
                                        </span>
                                    </div>
                                    <input type="range" class="form-range" id="fit-range-weight" min="40" max="110" step="1" value="65">
                                    <div class="d-flex justify-content-between text-muted extra-small">
                                        <span>40 kg</span>
                                        <span>65 kg</span>
                                        <span>110 kg</span>
                                    </div>
                                </div>

                                <!-- Chọn Phong Cách Mặc (Fit Preference) -->
                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-2">4. Phong cách mặc mong muốn:</label>
                                    <div class="row g-2" id="fit-preference-group">
                                        <div class="col-4">
                                            <input type="radio" class="btn-check" name="fit_preference" id="fit_pref_slim" value="slim">
                                            <label class="btn btn-outline-dark btn-sm w-100 text-start p-2 rounded-3 h-100 fit-pref-label" for="fit_pref_slim">
                                                <span class="fw-bold d-block small">Ôm gọn</span>
                                                <span class="text-muted" style="font-size: 0.7rem;">Slim Fit, vừa sát cơ thể</span>
                                            </label>
                                        </div>
                                        <div class="col-4">
                                            <input type="radio" class="btn-check" name="fit_preference" id="fit_pref_regular" value="regular" checked>
                                            <label class="btn btn-outline-dark btn-sm w-100 text-start p-2 rounded-3 h-100 fit-pref-label active" for="fit_pref_regular">
                                                <span class="fw-bold d-block small text-primary">Vừa vặn</span>
                                                <span class="text-muted" style="font-size: 0.7rem;">Regular Fit, thoải mái chuẩn</span>
                                            </label>
                                        </div>
                                        <div class="col-4">
                                            <input type="radio" class="btn-check" name="fit_preference" id="fit_pref_loose" value="loose">
                                            <label class="btn btn-outline-dark btn-sm w-100 text-start p-2 rounded-3 h-100 fit-pref-label" for="fit_pref_loose">
                                                <span class="fw-bold d-block small">Rộng rãi</span>
                                                <span class="text-muted" style="font-size: 0.7rem;">Oversize, tự do vận động</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" id="fit-save-pref-checkbox" checked>
                                    <label class="form-check-label extra-small text-muted" for="fit-save-pref-checkbox">
                                        Lưu số đo này để tự động gợi ý cho các sản phẩm khác khi xem shop
                                    </label>
                                </div>
                            </div>

                            <!-- Cột Thẻ Kết Quả Gợi Ý Thông Minh -->
                            <div class="col-lg-5">
                                <div class="card border-0 rounded-4 p-3.5 h-100 d-flex flex-column justify-content-between shadow-sm" style="background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="badge bg-warning text-dark fw-bold extra-small px-2.5 py-1 rounded-pill">
                                                <i class="fa-solid fa-sparkles me-1"></i>KẾT QUẢ GỢI Ý
                                            </span>
                                            <span class="extra-small text-white-50" id="fit-bmi-badge">BMI: 22.5 (Chuẩn)</span>
                                        </div>

                                        <div class="text-center py-3">
                                            <span class="text-white-50 extra-small text-uppercase tracking-wider d-block mb-1">Kích cỡ phù hợp nhất:</span>
                                            <div class="display-3 fw-extrabold text-warning lh-1 mb-2 font-monospace" id="fit-result-size-code">
                                                L
                                            </div>
                                            <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1 extra-small fw-semibold" id="fit-result-fit-name">
                                                Form Chuẩn Regular Fit
                                            </span>
                                        </div>

                                        <!-- Mức độ tương thích -->
                                        <div class="p-3 rounded-3 bg-white bg-opacity-10 mb-3 border border-white border-opacity-10">
                                            <div class="d-flex justify-content-between align-items-center extra-small mb-1.5">
                                                <span class="text-white-50">Độ tương thích với vóc dáng:</span>
                                                <strong class="text-warning font-monospace" id="fit-confidence-text">96% Phù Hợp</strong>
                                            </div>
                                            <div class="progress" style="height: 6px; background-color: rgba(255,255,255,0.2);">
                                                <div id="fit-confidence-bar" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: 96%;" aria-valuenow="96" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <!-- Lời khuyên cá nhân hoá -->
                                        <div class="extra-small text-white-50 mb-3 line-height-sm" id="fit-recommendation-note">
                                            <i class="fa-solid fa-circle-check text-success me-1"></i>
                                            <span>Với chiều cao 170cm và cân nặng 65kg, Size L mang lại tỷ lệ cân đối, tôn dáng và vừa vặn nhất cho cử động hàng ngày.</span>
                                        </div>
                                    </div>

                                    <!-- Nút Áp Dụng Chọn Size Vào Sản Phẩm -->
                                    <div class="mt-2 pt-2 border-top border-white border-opacity-10">
                                        <button type="button" class="btn btn-warning btn-lg w-100 rounded-pill fw-bold text-dark py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-2" id="btn-apply-recommended-size" onclick="applyAdvisorSize()">
                                            <i class="fa-solid fa-check"></i>
                                            <span>Chọn <strong id="btn-apply-size-text">Size L</strong> Cho Sản Phẩm</span>
                                        </button>
                                        <div id="fit-stock-warning" class="text-warning extra-small text-center mt-2 d-none">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                            <span>Size này hiện đang hết hàng ở một số màu sắc!</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================= TAB 2: BẢNG QUY ĐỔI THÔNG SỐ CHI TIẾT ================= -->
                    <div class="tab-pane fade" id="tab-chart" role="tabpanel" aria-labelledby="tab-chart-tab">
                        <!-- Sub-nav chuyển đổi Áo / Quần -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h6 class="fw-bold text-dark mb-0">Bảng Quy Đổi Thông Số Chuẩn ATINO:</h6>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-dark active fw-bold" id="btn-show-top-chart" onclick="switchChartTable('tops')">
                                    <i class="fa-solid fa-shirt me-1"></i> Bảng Size Áo
                                </button>
                                <button type="button" class="btn btn-outline-dark fw-bold" id="btn-show-bottom-chart" onclick="switchChartTable('bottoms')">
                                    <i class="fa-solid fa-vest-patches me-1"></i> Bảng Size Quần
                                </button>
                            </div>
                        </div>

                        <!-- Bảng Size Áo (Tops) -->
                        <div id="chart-table-tops" class="table-responsive">
                            <table class="table table-bordered table-hover align-middle text-center small mb-2">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="py-2.5">Size Áo</th>
                                        <th>Chiều Cao (cm)</th>
                                        <th>Cân Nặng (kg)</th>
                                        <th>Rộng Vai (cm)</th>
                                        <th>Vòng Ngực (cm)</th>
                                        <th>Dài Áo (cm)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-size-row="S">
                                        <td class="fw-bold bg-light">S</td>
                                        <td>155 - 164</td>
                                        <td>48 - 55</td>
                                        <td>42</td>
                                        <td>92</td>
                                        <td>66</td>
                                    </tr>
                                    <tr data-size-row="M">
                                        <td class="fw-bold bg-light">M</td>
                                        <td>165 - 170</td>
                                        <td>56 - 63</td>
                                        <td>44</td>
                                        <td>96</td>
                                        <td>68</td>
                                    </tr>
                                    <tr data-size-row="L" class="table-primary fw-semibold">
                                        <td class="fw-bold text-primary bg-primary bg-opacity-10">L (Phổ biến)</td>
                                        <td>171 - 176</td>
                                        <td>64 - 72</td>
                                        <td>46</td>
                                        <td>100</td>
                                        <td>70</td>
                                    </tr>
                                    <tr data-size-row="XL">
                                        <td class="fw-bold bg-light">XL</td>
                                        <td>177 - 182</td>
                                        <td>73 - 80</td>
                                        <td>48</td>
                                        <td>106</td>
                                        <td>72</td>
                                    </tr>
                                    <tr data-size-row="XXL">
                                        <td class="fw-bold bg-light">XXL</td>
                                        <td>183 - 190</td>
                                        <td>81 - 92</td>
                                        <td>50</td>
                                        <td>112</td>
                                        <td>74</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Bảng Size Quần (Bottoms) -->
                        <div id="chart-table-bottoms" class="table-responsive d-none">
                            <table class="table table-bordered table-hover align-middle text-center small mb-2">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="py-2.5">Size Quần</th>
                                        <th>Chiều Cao (cm)</th>
                                        <th>Cân Nặng (kg)</th>
                                        <th>Vòng Eo / Bụng (cm)</th>
                                        <th>Vòng Mông (cm)</th>
                                        <th>Dài Quần (cm)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-size-row="28">
                                        <td class="fw-bold bg-light">28</td>
                                        <td>155 - 162</td>
                                        <td>48 - 53</td>
                                        <td>72 - 74</td>
                                        <td>88 - 90</td>
                                        <td>92</td>
                                    </tr>
                                    <tr data-size-row="29">
                                        <td class="fw-bold bg-light">29</td>
                                        <td>160 - 167</td>
                                        <td>54 - 58</td>
                                        <td>75 - 77</td>
                                        <td>91 - 93</td>
                                        <td>94</td>
                                    </tr>
                                    <tr data-size-row="30">
                                        <td class="fw-bold bg-light">30</td>
                                        <td>165 - 172</td>
                                        <td>59 - 64</td>
                                        <td>78 - 80</td>
                                        <td>94 - 96</td>
                                        <td>96</td>
                                    </tr>
                                    <tr data-size-row="31" class="table-primary fw-semibold">
                                        <td class="fw-bold text-primary bg-primary bg-opacity-10">31 (Phổ biến)</td>
                                        <td>170 - 176</td>
                                        <td>65 - 70</td>
                                        <td>81 - 83</td>
                                        <td>97 - 99</td>
                                        <td>98</td>
                                    </tr>
                                    <tr data-size-row="32">
                                        <td class="fw-bold bg-light">32</td>
                                        <td>173 - 180</td>
                                        <td>71 - 76</td>
                                        <td>84 - 86</td>
                                        <td>100 - 103</td>
                                        <td>100</td>
                                    </tr>
                                    <tr data-size-row="33">
                                        <td class="fw-bold bg-light">33</td>
                                        <td>175 - 182</td>
                                        <td>76 - 80</td>
                                        <td>86 - 89</td>
                                        <td>102 - 105</td>
                                        <td>101</td>
                                    </tr>
                                    <tr data-size-row="34">
                                        <td class="fw-bold bg-light">34</td>
                                        <td>177 - 185</td>
                                        <td>80 - 87</td>
                                        <td>89 - 93</td>
                                        <td>105 - 109</td>
                                        <td>103</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 p-3 bg-light rounded-3 border extra-small text-muted d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-info text-primary fs-6 flex-shrink-0"></i>
                            <div>
                                Nếu thông số chiều cao và cân nặng rơi vào 2 khoảng size khác nhau, quý khách nên ưu tiên chọn theo <strong>Cân Nặng</strong> để đảm bảo sự thoải mái khi mặc.
                            </div>
                        </div>
                    </div>

                    <!-- ================= TAB 3: HƯỚNG DẪN ĐO TẠI NHÀ ================= -->
                    <div class="tab-pane fade" id="tab-guide" role="tabpanel" aria-labelledby="tab-guide-tab">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 border rounded-3 bg-white h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold small">
                                        <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">1</span>
                                        <span>Đo Vòng Ngực (Bust)</span>
                                    </div>
                                    <p class="extra-small text-muted mb-0">
                                        Dùng thước dây quấn quanh điểm nở nhất của ngực, giữ thước dây thẳng nằm ngang dưới nách và qua bả vai. Không siết quá chặt thước.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 border rounded-3 bg-white h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold small">
                                        <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">2</span>
                                        <span>Đo Vòng Eo / Bụng (Waist)</span>
                                    </div>
                                    <p class="extra-small text-muted mb-0">
                                        Đo quanh vòng eo tự nhiên (vị trí hẹp nhất ở phần thân trên, cách rốn khoảng 2-3cm). Thở đều tự nhiên khi đo để lấy số đo chuẩn.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 border rounded-3 bg-white h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold small">
                                        <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">3</span>
                                        <span>Đo Chiều Dài Áo (Length)</span>
                                    </div>
                                    <p class="extra-small text-muted mb-0">
                                        Đo từ điểm cao nhất của đường may vai (chỗ tiếp giáp với cổ áo) thẳng xuống mép dưới gấu áo mong muốn.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 border rounded-3 bg-white h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold small">
                                        <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">4</span>
                                        <span>Đo Chiều Dài Quần (Inseam)</span>
                                    </div>
                                    <p class="extra-small text-muted mb-0">
                                        Đo từ cạp quần dọc theo mép ngoài thân quần thẳng xuống tới mắt cá chân (hoặc chạm mép mu bàn chân tùy dáng quần).
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 rounded-3 mt-3.5 mb-0 p-3 extra-small d-flex align-items-center gap-2.5">
                            <i class="fa-solid fa-shield-heart text-warning fs-5 flex-shrink-0"></i>
                            <div>
                                <strong>Chính sách đổi size an tâm 100%:</strong> ATINO hỗ trợ đổi size tận nhà trong vòng 30 ngày hoàn toàn miễn phí nếu sản phẩm nhận về mặc không vừa vặn.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fit-pref-label {
        transition: all 0.2s ease;
        border-color: #e2e8f0 !important;
    }
    .fit-pref-label:hover {
        border-color: #0f172a !important;
    }
    .btn-check:checked + .fit-pref-label {
        background-color: #f8fafc !important;
        border-color: #0f172a !important;
        box-shadow: 0 0 0 1px #0f172a;
    }
    #fit-apparel-type-group .btn-check:checked + .btn {
        background-color: #0f172a !important;
        color: #ffffff !important;
    }
    .form-range::-webkit-slider-thumb {
        background: #0f172a;
    }
    .form-range::-moz-range-thumb {
        background: #0f172a;
    }
</style>
