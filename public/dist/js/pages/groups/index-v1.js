import {
  sendHttpRequest,
  HOST_URL,
  setLoadingState,
  resetLoadingState,
} from '../utils/utils.js';
const addBtn = document.querySelector('.btn-success');
const alertBox = document.querySelector('#alertBox');
const errorMessage = document.querySelector('.error-message');
const groupSelect = document.querySelector('#group');
const districtSelect = document.querySelector('#district');
const btnSave = document.querySelector('.btnsave');
const membersSelect = document.querySelector('#members');
const form = document.querySelector('.add-membership');
const selectedMembers = [];
const fetchedMembers = [];

function activateMultiSelect() {
  $(function () {
    $('#members').multiselect({
      includeSelectAllOption: true,
      buttonWidth: '100%',
      onChange: function (option, checked, select) {
        var members = $('#members option:selected');
        $(members).each(function () {
          const isSelected = selectedMembers.some(
            member => member.value === $(this).val()
          );
          if (!isSelected) {
            selectedMembers.push({
              value: $(this).val(),
              label: $(this).text(),
            });
          }
        });
      },
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
  setLoadingState(btnSave, 'Saving...');
  const response = await sendHttpRequest(
    `${HOST_URL}/groups/addmembership`,
    'POST',
    JSON.stringify(data),
    { 'Content-Type': 'application/json' },
    alertBox
  );
  resetLoadingState(btnSave, 'Add Membership(s)');

  if (!response.success) {
    displayError(response.message);
    return;
  } else {
    $('#members').multiselect('deselectAll', true);
    groupSelect.value = '';
    window.location.reload();
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
  if (e.target.value === '' || districtSelect.value === '') return;
  loadMembers();
  // membersSelect.innerHTML = '';
  // $('#members').multiselect('destroy');
  // let options = '';

  // const response = await sendHttpRequest(
  //   `${HOST_URL}/groups/getmembers?group=${e.target.value}`
  // );
  // if (response.success) {
  //   const { data } = response;
  //   data.forEach(dt => {
  //     options = options + `<option value="${dt.id}">${dt.memberName}</option>`;
  //   });
  //   membersSelect.innerHTML = options;
  //   activateMultiSelect();
  // }
});

districtSelect.addEventListener('change', async function (e) {
  if (e.target.value === '' || districtSelect.value === '') return;
  loadMembers();
});

async function loadMembers() {
  const districtValue = districtSelect.value;
  const groupValue = groupSelect.value;
  if (
    !districtValue ||
    !groupValue ||
    groupValue.value === '' ||
    districtValue.value === ''
  )
    return;

  const response = await sendHttpRequest(
    `${HOST_URL}/groups/getmembers?group=${groupValue}&district=${districtValue}`
  );
  if (response.success) {
    const { data } = response;
    fetchedMembers.splice(0);
    data.forEach(dt => {
      const memberWasSelected = selectedMembers.some(
        mbr => mbr.value === member.value
      );
      if (!memberWasSelected) {
        fetchedMembers.push({ value: dt.id, label: dt.memberName });
      }
    });
    const enjoinedMembers = [...selectedMembers, ...fetchedMembers];
    bindMembers(enjoinedMembers);
  }
}

function bindMembers(allMembers) {
  membersSelect.innerHTML = '';
  $('#members').multiselect('destroy');

  let options = '';
  allMembers.forEach(member => {
    const memberWasSelected = selectedMembers.some(
      mbr => mbr.value === member.value
    );
    options =
      options +
      `<option value="${member.value}" ${memberWasSelected && 'selected'}>${
        member.label
      }</option>`;
  });
  membersSelect.innerHTML = options;
  activateMultiSelect();
}
