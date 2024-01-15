import {
  mandatoryFields,
  sendHttpRequest,
  alertBox,
  validation,
  clearOnChange,
  HOST_URL,
} from '../utils/utils.js';
const districtsSelect = document.getElementById('district');
const membersSelect = document.getElementById('deacon');
const form = document.querySelector('form');

districtsSelect.addEventListener('change', async function (e) {
  const value = e.target.value;
  if (!value || value.trim().length === 0) return;

  const response = await sendHttpRequest(
    `${HOST_URL}/deacons/getmembers?district=${value}`,
    'GET',
    undefined,
    {},
    alertBox
  );

  if (response.success) {
    let option = '<option selected disabled>Select deacon...</option>';
    response.members.forEach(member => {
      option =
        option + `<option value="${member.ID}">${member.memberName}</option>`;
    });
    membersSelect.innerHTML = option;
  }
});

form.addEventListener('submit', function (e) {
  e.preventDefault();
  if (validation() > 0) return;
  this.submit();
});

clearOnChange(mandatoryFields);
