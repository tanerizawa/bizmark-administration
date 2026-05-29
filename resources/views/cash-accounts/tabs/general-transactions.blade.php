@php
    $generalIncome = $generalTransactions['income'] ?? collect();
    $generalExpenses = $generalTransactions['expenses'] ?? collect();
    $totalIncome = $generalIncome->sum('amount');
    $totalExpenses = $generalExpenses->sum('amount');
    $netGeneral = $totalIncome - $totalExpenses;
@endphp

<div style="display:flex;flex-direction:column;gap:16px">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:8px">
            <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-purple) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-briefcase" style="color:var(--apple-purple);font-size:0.72rem"></i></span>
            <div>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Keuangan Umum Perusahaan</h3>
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:2px 0 0">Pemasukan dan pengeluaran tidak terkait proyek tertentu</p>
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button onclick="openGeneralIncomeModal()"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:var(--apple-green);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <i class="fas fa-plus-circle" style="font-size:0.75rem"></i>Pemasukan Umum
            </button>
            <button onclick="openGeneralExpenseModal()"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;background:rgba(255,255,255,.05);cursor:pointer"
                    onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-minus-circle" style="font-size:0.75rem"></i>Pengeluaran Umum
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        <div style="padding:14px 16px;border-radius:14px;background:color-mix(in srgb,var(--apple-green) 8%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 18%,transparent)">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0 0 3px">Total Pemasukan Umum</p>
                    <p style="font-size:1.05rem;font-weight:800;color:var(--apple-green);margin:0">Rp {{ number_format($totalIncome) }}</p>
                </div>
                <div style="width:36px;height:36px;border-radius:50%;background:color-mix(in srgb,var(--apple-green) 15%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-arrow-down" style="color:var(--apple-green);font-size:0.85rem"></i></div>
            </div>
        </div>
        <div style="padding:14px 16px;border-radius:14px;background:color-mix(in srgb,var(--apple-red) 8%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 18%,transparent)">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0 0 3px">Total Pengeluaran Umum</p>
                    <p style="font-size:1.05rem;font-weight:800;color:var(--apple-red);margin:0">Rp {{ number_format($totalExpenses) }}</p>
                </div>
                <div style="width:36px;height:36px;border-radius:50%;background:color-mix(in srgb,var(--apple-red) 15%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-arrow-up" style="color:var(--apple-red);font-size:0.85rem"></i></div>
            </div>
        </div>
        <div style="padding:14px 16px;border-radius:14px;background:color-mix(in srgb,var(--apple-blue) 8%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 18%,transparent)">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0 0 3px">Selisih</p>
                    <p style="font-size:1.05rem;font-weight:800;color:{{ $netGeneral >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }};margin:0">{{ $netGeneral >= 0 ? '+' : '' }}Rp {{ number_format($netGeneral) }}</p>
                </div>
                <div style="width:36px;height:36px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-balance-scale" style="color:var(--apple-blue);font-size:0.85rem"></i></div>
            </div>
        </div>
    </div>

    @if($generalIncome->count() > 0 || $generalExpenses->count() > 0)
        {{-- Income Section --}}
        @if($generalIncome->count() > 0)
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
            <div style="display:flex;align-items:center;gap:8px;padding:12px 16px;border-bottom:1px solid var(--dark-separator)">
                <span style="width:22px;height:22px;border-radius:7px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-plus-circle" style="color:var(--apple-green);font-size:0.68rem"></i></span>
                <h4 style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary);margin:0">Pemasukan Umum ({{ $generalIncome->count() }})</h4>
            </div>
            <div style="padding:12px;display:flex;flex-direction:column;gap:8px">
                @foreach($generalIncome as $income)
                <div style="padding:12px 14px;border-radius:10px;background:color-mix(in srgb,var(--apple-green) 5%,transparent);border-left:3px solid color-mix(in srgb,var(--apple-green) 50%,transparent)">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start">
                        <div style="flex:1">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                                <span style="font-size:0.78rem;font-weight:600;color:var(--dark-text-primary)">{{ \Carbon\Carbon::parse($income->payment_date)->isoFormat('D MMM Y') }}</span>
                                <span style="display:inline-flex;padding:2px 8px;border-radius:8px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)">{{ ucfirst($income->payment_method) }}</span>
                            </div>
                            <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0">{{ $income->description ?? 'Pemasukan Umum' }}</p>
                            @if($income->bankAccount)<p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:3px 0 0"><i class="fas fa-university" style="margin-right:4px"></i>{{ $income->bankAccount->account_name }}</p>@endif
                        </div>
                        <div style="text-align:right;flex-shrink:0;margin-left:12px">
                            <p style="font-size:0.88rem;font-weight:700;color:var(--apple-green);margin:0 0 6px">+Rp {{ number_format($income->amount) }}</p>
                            <div style="display:flex;gap:4px;justify-content:flex-end">
                                <button onclick="editGeneralIncome({{ $income->id }})"
                                        title="Edit"
                                        style="width:26px;height:26px;border-radius:7px;background:rgba(255,255,255,.06);border:none;color:var(--dark-text-secondary);font-size:0.72rem;cursor:pointer;display:inline-flex;align-items:center;justify-content:center"
                                        onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='rgba(255,255,255,.06)'">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteGeneralIncome({{ $income->id }})"
                                        title="Hapus"
                                        style="width:26px;height:26px;border-radius:7px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:none;color:var(--apple-red);font-size:0.72rem;cursor:pointer;display:inline-flex;align-items:center;justify-content:center"
                                        onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Expense Section --}}
        @if($generalExpenses->count() > 0)
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
            <div style="display:flex;align-items:center;gap:8px;padding:12px 16px;border-bottom:1px solid var(--dark-separator)">
                <span style="width:22px;height:22px;border-radius:7px;background:color-mix(in srgb,var(--apple-red) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-minus-circle" style="color:var(--apple-red);font-size:0.68rem"></i></span>
                <h4 style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary);margin:0">Pengeluaran Umum ({{ $generalExpenses->count() }})</h4>
            </div>
            <div style="padding:12px;display:flex;flex-direction:column;gap:8px">
                @foreach($generalExpenses as $expense)
                <div style="padding:12px 14px;border-radius:10px;background:color-mix(in srgb,var(--apple-red) 5%,transparent);border-left:3px solid color-mix(in srgb,var(--apple-red) 50%,transparent)">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start">
                        <div style="flex:1">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                                <span style="font-size:0.78rem;font-weight:600;color:var(--dark-text-primary)">{{ \Carbon\Carbon::parse($expense->expense_date)->isoFormat('D MMM Y') }}</span>
                                <span style="display:inline-flex;padding:2px 8px;border-radius:8px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange)">{{ $expense->category_name }}</span>
                            </div>
                            <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0">{{ $expense->description ?? $expense->vendor_name ?? 'Pengeluaran Umum' }}</p>
                            @if($expense->bankAccount)<p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:3px 0 0"><i class="fas fa-university" style="margin-right:4px"></i>{{ $expense->bankAccount->account_name }}</p>@endif
                        </div>
                        <div style="text-align:right;flex-shrink:0;margin-left:12px">
                            <p style="font-size:0.88rem;font-weight:700;color:var(--apple-red);margin:0 0 6px">-Rp {{ number_format($expense->amount) }}</p>
                            <div style="display:flex;gap:4px;justify-content:flex-end">
                                <button onclick="editGeneralExpense({{ $expense->id }})"
                                        title="Edit"
                                        style="width:26px;height:26px;border-radius:7px;background:rgba(255,255,255,.06);border:none;color:var(--dark-text-secondary);font-size:0.72rem;cursor:pointer;display:inline-flex;align-items:center;justify-content:center"
                                        onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='rgba(255,255,255,.06)'">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteGeneralExpense({{ $expense->id }})"
                                        title="Hapus"
                                        style="width:26px;height:26px;border-radius:7px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:none;color:var(--apple-red);font-size:0.72rem;cursor:pointer;display:inline-flex;align-items:center;justify-content:center"
                                        onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @else
        <div style="text-align:center;padding:48px 20px">
            <div style="width:52px;height:52px;border-radius:50%;background:color-mix(in srgb,var(--apple-purple) 12%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 12px"><i class="fas fa-briefcase" style="color:var(--apple-purple);font-size:1.2rem"></i></div>
            <p style="font-size:0.92rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Transaksi Keuangan Umum</p>
            <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">Klik tombol di atas untuk menambahkan pemasukan atau pengeluaran umum</p>
        </div>
    @endif
</div>

{{-- General Income Modal --}}
<div id="generalIncomeModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.72)">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;width:100%;max-width:480px;box-shadow:0 24px 60px rgba(0,0,0,.6)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
            <h5 id="incomeModalTitle" style="font-size:0.92rem;font-weight:700;color:var(--dark-text-primary);margin:0">Tambah Pemasukan Umum</h5>
            <button onclick="closeGeneralIncomeModal()" style="width:30px;height:30px;border-radius:50%;border:none;background:rgba(255,255,255,.06);color:var(--dark-text-secondary);cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center">&times;</button>
        </div>
        <div style="padding:20px">
            <form id="generalIncomeForm" onsubmit="event.preventDefault();submitGeneralIncome();" style="display:flex;flex-direction:column;gap:12px">
                @php $is = 'width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box'; @endphp
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tanggal Pemasukan</label>
                    <input type="date" id="income_payment_date" name="payment_date" required style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Jumlah (Rp)</label>
                    <input type="text" id="income_amount_display" placeholder="0.00" inputmode="decimal" style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    <input type="hidden" name="amount" id="income_amount">
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Metode Pembayaran</label>
                    <select id="income_payment_method" name="payment_method" required style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Pilih Metode</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="cash">Tunai</option>
                        <option value="check">Cek</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Akun Kas/Bank</label>
                    <select id="income_bank_account_id" name="bank_account_id" required style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Pilih Akun</option>
                        @foreach($cashAccountsList as $account)
                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nomor Referensi (Opsional)</label>
                    <input type="text" id="income_reference_number" name="reference_number" placeholder="No. Bukti/Referensi" style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Keterangan</label>
                    <textarea id="income_description" name="description" rows="3" placeholder="Deskripsi pemasukan umum..." style="{{ $is }};resize:vertical" onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'"></textarea>
                </div>
            </form>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <button type="button" onclick="closeGeneralIncomeModal()"
                    style="padding:8px 18px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;background:var(--dark-bg-tertiary);cursor:pointer">Batal</button>
            <button type="button" id="incomeSubmitBtn" onclick="submitGeneralIncome()"
                    style="padding:8px 20px;border:none;border-radius:9px;background:var(--apple-green);color:#fff;font-size:0.82rem;font-weight:600;cursor:pointer"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">Simpan Pemasukan</button>
        </div>
    </div>
</div>

{{-- General Expense Modal --}}
<div id="generalExpenseModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.72)">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;width:100%;max-width:480px;box-shadow:0 24px 60px rgba(0,0,0,.6)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
            <h5 id="expenseModalTitle" style="font-size:0.92rem;font-weight:700;color:var(--dark-text-primary);margin:0">Tambah Pengeluaran Umum</h5>
            <button onclick="closeGeneralExpenseModal()" style="width:30px;height:30px;border-radius:50%;border:none;background:rgba(255,255,255,.06);color:var(--dark-text-secondary);cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center">&times;</button>
        </div>
        <div style="padding:20px;max-height:70vh;overflow-y:auto">
            <form id="generalExpenseForm" onsubmit="event.preventDefault();submitGeneralExpense();" style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tanggal Pengeluaran</label>
                    <input type="date" id="expense_expense_date" name="expense_date" required style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Jumlah (Rp)</label>
                    <input type="text" id="expense_amount_display" placeholder="0.00" inputmode="decimal" style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    <input type="hidden" name="amount" id="expense_amount">
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Kategori</label>
                    <select id="expense_category" name="category" required style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Pilih Kategori</option>
                        @foreach($expenseCategories as $groupName => $items)
                            <optgroup label="{{ $groupName }}">
                                @foreach($items as $category)
                                    <option value="{{ $category['value'] }}">{{ $category['icon'] }} {{ $category['label'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Metode Pembayaran</label>
                    <select id="expense_payment_method" name="payment_method" required style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Pilih Metode</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="cash">Tunai</option>
                        <option value="check">Cek</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Akun Kas/Bank</label>
                    <select id="expense_bank_account_id" name="bank_account_id" required style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Pilih Akun</option>
                        @foreach($cashAccountsList as $account)
                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Vendor/Penerima (Opsional)</label>
                    <input type="text" id="expense_vendor_name" name="vendor_name" placeholder="Nama vendor atau penerima" style="{{ $is }}" onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Keterangan</label>
                    <textarea id="expense_description" name="description" rows="3" placeholder="Deskripsi pengeluaran umum..." style="{{ $is }};resize:vertical" onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'"></textarea>
                </div>
            </form>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <button type="button" onclick="closeGeneralExpenseModal()"
                    style="padding:8px 18px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;background:var(--dark-bg-tertiary);cursor:pointer">Batal</button>
            <button type="button" id="expenseSubmitBtn" onclick="submitGeneralExpense()"
                    style="padding:8px 20px;border:none;border-radius:9px;background:var(--apple-red);color:#fff;font-size:0.82rem;font-weight:600;cursor:pointer"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">Simpan Pengeluaran</button>
        </div>
    </div>
</div>

<script>
let editingIncomeId = null, editingExpenseId = null;

function openGeneralIncomeModal() {
    editingIncomeId = null;
    document.getElementById('generalIncomeForm').reset();
    document.getElementById('incomeModalTitle').textContent = 'Tambah Pemasukan Umum';
    document.getElementById('incomeSubmitBtn').textContent = 'Simpan Pemasukan';
    document.getElementById('generalIncomeModal').style.display = 'flex';
}
function closeGeneralIncomeModal() {
    document.getElementById('generalIncomeModal').style.display = 'none';
}
function openGeneralExpenseModal() {
    editingExpenseId = null;
    document.getElementById('generalExpenseForm').reset();
    document.getElementById('expenseModalTitle').textContent = 'Tambah Pengeluaran Umum';
    document.getElementById('expenseSubmitBtn').textContent = 'Simpan Pengeluaran';
    document.getElementById('generalExpenseModal').style.display = 'flex';
}
function closeGeneralExpenseModal() {
    document.getElementById('generalExpenseModal').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeGeneralIncomeModal(); closeGeneralExpenseModal(); }
});
document.getElementById('generalIncomeModal').addEventListener('click', function(e) { if (e.target === this) closeGeneralIncomeModal(); });
document.getElementById('generalExpenseModal').addEventListener('click', function(e) { if (e.target === this) closeGeneralExpenseModal(); });

function editGeneralIncome(id) {
    editingIncomeId = id;
    document.getElementById('incomeModalTitle').textContent = 'Edit Pemasukan Umum';
    document.getElementById('incomeSubmitBtn').textContent = 'Update Pemasukan';
    fetch(`/general-transactions/income/${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const d = data.data;
                document.getElementById('income_payment_date').value = d.payment_date;
                document.getElementById('income_amount').value = d.amount;
                if (typeof CurrencyHelper !== 'undefined') {
                    document.getElementById('income_amount_display').value = CurrencyHelper.format(d.amount);
                } else {
                    document.getElementById('income_amount_display').value = d.amount;
                }
                document.getElementById('income_payment_method').value = d.payment_method;
                document.getElementById('income_bank_account_id').value = d.bank_account_id;
                document.getElementById('income_description').value = d.description || '';
                document.getElementById('income_reference_number').value = d.reference_number || '';
            } else { Swal.fire('Error', 'Gagal memuat data pemasukan', 'error'); }
        })
        .catch(() => Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error'));
    document.getElementById('generalIncomeModal').style.display = 'flex';
}

function deleteGeneralIncome(id) {
    Swal.fire({ title: 'Hapus Pemasukan Umum?', text: 'Data tidak dapat dikembalikan', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' })
    .then(result => {
        if (result.isConfirmed) {
            fetch(`/general-transactions/income/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => { if (data.success) { Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload()); } else { Swal.fire('Error', data.message, 'error'); } })
            .catch(() => Swal.fire('Error', 'Terjadi kesalahan saat menghapus', 'error'));
        }
    });
}

function editGeneralExpense(id) {
    editingExpenseId = id;
    document.getElementById('expenseModalTitle').textContent = 'Edit Pengeluaran Umum';
    document.getElementById('expenseSubmitBtn').textContent = 'Update Pengeluaran';
    fetch(`/general-transactions/expense/${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const d = data.data;
                document.getElementById('expense_expense_date').value = d.expense_date;
                document.getElementById('expense_amount').value = d.amount;
                if (typeof CurrencyHelper !== 'undefined') {
                    document.getElementById('expense_amount_display').value = CurrencyHelper.format(d.amount);
                } else {
                    document.getElementById('expense_amount_display').value = d.amount;
                }
                document.getElementById('expense_category').value = d.category;
                document.getElementById('expense_payment_method').value = d.payment_method;
                document.getElementById('expense_bank_account_id').value = d.bank_account_id;
                document.getElementById('expense_vendor_name').value = d.vendor_name || '';
                document.getElementById('expense_description').value = d.description || '';
            } else { Swal.fire('Error', 'Gagal memuat data pengeluaran', 'error'); }
        })
        .catch(() => Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error'));
    document.getElementById('generalExpenseModal').style.display = 'flex';
}

function deleteGeneralExpense(id) {
    Swal.fire({ title: 'Hapus Pengeluaran Umum?', text: 'Data tidak dapat dikembalikan', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' })
    .then(result => {
        if (result.isConfirmed) {
            fetch(`/general-transactions/expense/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => { if (data.success) { Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload()); } else { Swal.fire('Error', data.message, 'error'); } })
            .catch(() => Swal.fire('Error', 'Terjadi kesalahan saat menghapus', 'error'));
        }
    });
}

function submitGeneralIncome() {
    const displayInput = document.getElementById('income_amount_display');
    const hiddenInput = document.getElementById('income_amount');
    if (displayInput && hiddenInput) {
        const parsedValue = typeof CurrencyHelper !== 'undefined' ? CurrencyHelper.parse(displayInput.value) : parseFloat(displayInput.value.replace(/[^0-9.-]/g, ''));
        hiddenInput.value = parsedValue;
        if (!parsedValue || parsedValue <= 0) { alert('Jumlah pemasukan harus diisi dengan benar!'); displayInput.focus(); return; }
    }
    const form = document.getElementById('generalIncomeForm');
    const formData = new FormData(form);
    const url = editingIncomeId ? `/general-transactions/income/${editingIncomeId}` : '/general-transactions/income';
    const method = editingIncomeId ? 'PUT' : 'POST';
    const data = {};
    formData.forEach((v, k) => data[k] = v);
    fetch(url, { method, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(data) })
    .then(r => r.json())
    .then(data => { if (data.success) { closeGeneralIncomeModal(); Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload()); } else { Swal.fire('Error', data.message, 'error'); } })
    .catch(() => Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error'));
}

function submitGeneralExpense() {
    const displayInput = document.getElementById('expense_amount_display');
    const hiddenInput = document.getElementById('expense_amount');
    if (displayInput && hiddenInput) {
        const parsedValue = typeof CurrencyHelper !== 'undefined' ? CurrencyHelper.parse(displayInput.value) : parseFloat(displayInput.value.replace(/[^0-9.-]/g, ''));
        hiddenInput.value = parsedValue;
        if (!parsedValue || parsedValue <= 0) { alert('Jumlah pengeluaran harus diisi dengan benar!'); displayInput.focus(); return; }
    }
    const form = document.getElementById('generalExpenseForm');
    const formData = new FormData(form);
    const url = editingExpenseId ? `/general-transactions/expense/${editingExpenseId}` : '/general-transactions/expense';
    const method = editingExpenseId ? 'PUT' : 'POST';
    const data = {};
    formData.forEach((v, k) => data[k] = v);
    fetch(url, { method, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(data) })
    .then(r => r.json())
    .then(data => { if (data.success) { closeGeneralExpenseModal(); Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload()); } else { Swal.fire('Error', data.message, 'error'); } })
    .catch(() => Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error'));
}

// Setup CurrencyHelper
(function() {
    function initCurrencyInputs() {
        if (typeof CurrencyHelper === 'undefined') { setTimeout(initCurrencyInputs, 100); return; }
        try {
            CurrencyHelper.setupInput('income_amount_display', 'income_amount', { decimals: 2, maxValue: 9999999999.99, allowNegative: false });
            CurrencyHelper.setupInput('expense_amount_display', 'expense_amount', { decimals: 2, maxValue: 9999999999.99, allowNegative: false });
        } catch(e) { console.error('Error initializing currency inputs:', e); }
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initCurrencyInputs); } else { initCurrencyInputs(); }
})();
</script>
