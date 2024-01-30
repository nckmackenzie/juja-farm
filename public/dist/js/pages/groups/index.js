import { sendHttpRequest, HOST_URL } from '../utils/utils.js';
const addBtn = document.querySelector('.btn-success');
const alertBox = document.querySelector('#alertBox');
const errorMessage = document.querySelector('.error-message');
const groupSelect = document.querySelector('#group');
const membersSelect = document.querySelector('#members');
const form = document.querySelector('.add-membership');

function activateMultiSelect() {
  $(function () {
    $('#members').multiselect({
      includeSelectAllOption: true,
      buttonWidth: '100%',
    });
  });
}

addBtn.addEventListener('click', function () {
  $('#addModalCenter').modal('show');
});

form.addEventListener('submit', async e => {
  e.preventDefault();
  clearError();
  const group = groupSelect.value;
  const membersArr = [];
  $('#members :selected').each(function (i, sel) {
    membersArr.push($(sel).val());
  });
  if (group === '') {
    displayError('Select group;');
    return;
  }
  if (membersArr.length === 0) {
    displayError('Select at least one member;');
    return;
  }

  const data = { group, members: membersArr };

  const response = await sendHttpRequest(
    `${HOST_URL}/groups/addmembership`,
    'POST',
    JSON.stringify(data),
    { 'Content-Type': 'application/json' },
    alertBox
  );

  if (!response.success) {
    displayError(response.message);
    return;
  } else {
    $('#members').multiselect('deselectAll', true);
    groupSelect.value = '';
  }
});

function displayError(message) {
  alertBox.classList.remove('d-none');
  alertBox.classList.add('d-block');
  errorMessage.textContent = message;
}

function clearError() {
  alertBox.classList.remove('d-block');
  alertBox.classList.add('d-none');
  errorMessage.textContent = '';
}

groupSelect.addEventListener('change', async function (e) {
  if (e.target.value === '') return;
  membersSelect.innerHTML = '';
  $('#members').multiselect('destroy');
  let options = '';

  const response = await sendHttpRequest(
    `${HOST_URL}/groups/getmembers?group=${e.target.value}`
  );
  if (response.success) {
    const { data } = response;
    data.forEach(dt => {
      options = options + `<option value="${dt.id}">${dt.memberName}</option>`;
    });
    membersSelect.innerHTML = options;
    activateMultiSelect();
  }
});
