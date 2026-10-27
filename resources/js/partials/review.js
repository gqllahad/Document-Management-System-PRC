// review (are u sure naba? )
(() => {
  const dlg  = document.getElementById('reviewModal');
  const list = document.getElementById('rvList');
  const warn = document.getElementById('rvWarn');
  const $ = id => document.getElementById(id);
  const num = v => parseFloat(String(v).replace(/[^\d.]/g, '')) || 0;
  const peso = n => '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2 });
  let pending = null;

  const openReview = (form, rows, warning = '') => {
    list.innerHTML = '';
    rows.forEach(([label, value, wide]) => {
      const d = document.createElement('div');
      if (wide) d.className = 'wide';
      const dt = document.createElement('dt'); dt.textContent = label;
      const dd = document.createElement('dd'); dd.textContent = value || '—';
      d.append(dt, dd);
      list.append(d);
    });

    warn.hidden = !warning;
    warn.querySelector('span').textContent = warning;
    pending = form;
    dlg.showModal();
  };

  // Stage 1
  const create = $('createForm');
  create?.addEventListener('submit', e => {
    e.preventDefault();
    if (!create.reportValidity()) return;

  //   openReview(create, [
  //     ['Project name', create.project_name.value, true],
  //     ['Project type', create.project_type.value],
  //     ['Approved budget (ABC)', peso(num(create.approved_budget.value))],
  //     ['Source of funds', create.source_funds.value, true],
  // ]);
    const structure = create.querySelector(
      'input[name="procurement_structure"]:checked'
  )?.value;

  const unitInputs = [
      ...create.querySelectorAll('input[name="units[]"]:not(:disabled)')
  ];

  const rows = [
      ['Project name', create.project_name.value, true],
      [
          'Project type', create.project_type.value, true
      ],
      [
          'Approved budget (ABC)',
          peso(num(create.approved_budget.value))
      ],
      ['Source of funds', create.source_funds.value, true],
      [
          'Procurement structure',
          structure === 'multiple' ? 'Multiple Units' : 'Main Contract',
          true
      ],
      ['Quarter',create.quarter.value, true],
  ];

  if (structure === 'multiple') {
      unitInputs.forEach((input, index) => {
          rows.push([
              `Unit ${index + 1}`,
              input.value,
              true
          ]);
      });
  }

  openReview(create, rows);

  });

  // Stage 2
  const cont = $('continueForm');
  cont?.addEventListener('submit', e => {
    e.preventDefault();
    if (!cont.reportValidity()) return;

    const price = num($('contract_price').value);
    const abc   = num($('cAbc').textContent);
    const files = [...$('documents').files].map(f => f.name);
    const status = $('status_id');

    openReview(cont, [
      ['Project', $('cName').textContent, true],
      ['Contract number', $('cContract').value],
      ['Contract price', peso(price)],
      ['Contract date', $('contract_date').value],
      ['winning_bidder', $('winning_bidder').value],
    //   ['Period', $('periodOut').textContent],
      ['Status', status.options[status.selectedIndex].text],
      ['Status date', $('update_date').value],
      ['Current status', $('update').value, true],
      ['Documents', files.length ? files.join(', ') : 'No files attached', true],
    ], abc && price > abc ? `Contract price exceeds the ABC (${peso(abc)}).` : '');
  });


  // Stage 3
  const ntpForm = $('ntpForm');

  ntpForm?.addEventListener('submit', e => {
    e.preventDefault();

    if (!ntpForm.reportValidity()) return;

    const ntpDate = $('ntp_date').value;
    const period = Number($('delivery_period').value);
    const status = $('status_id3');
    const statusDate = $('update_date3').value;
    const delivered = $('actual_date_of_delivery');
    const files = [...$('documents3').files].map(f => f.name);


    let expectedDate = '—';

    if (ntpDate && period > 0) {
      const date = new Date(ntpDate + 'T00:00:00');
      date.setDate(date.getDate() + period);

      expectedDate = date.toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }

    openReview(ntpForm, [
      ['Project', $('nName').textContent, true],
      ['Contract number', $('nContract').textContent],
      ['Winning bidder', $('nWinningBidder').textContent],
      ['Contract price', '₱' + ($('nPrice').textContent || '0')],
      ['NTP date', ntpDate],
      ['Delivery period', period + ' calendar days'],
      ['Expected delivery', expectedDate],
      ['Status',status.options[status.selectedIndex].text],
      ...(delivered && !delivered.disabled ? [['Actual delivery', delivered.value]] : []),
      ['Status date', statusDate],
      ['Current status', $('update3').value, true],
      ['Documents',files.length ? files.join(', ') : 'No files attached',true],
    ]);
  });

  dlg?.addEventListener('click', e => {
    if (e.target === dlg || e.target.closest('[data-review-back]')) dlg.close();
    if (e.target.closest('[data-review-confirm]')) {
      dlg.close();
      pending?.submit();
    }
  });
})();