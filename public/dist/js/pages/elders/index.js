import { getDistricts } from '../members/ajax.js';
import { mandatoryFields, clearOnChange, validation } from '../utils/utils.js';
const congSelect = document.getElementById('congregation');
const districtSelect = document.getElementById('district');
const form = document.querySelector('form');

congSelect.addEventListener('change', async function (e) {
  if (!e.target.value || e.target.value === '') return;
  districtSelect.innerHTML = await getDistricts(e.target.value);
});

form.addEventListener('submit', e => {
  e.preventDefault();
  if (validation() > 0) return;
  document.form.submit();
});

clearOnChange(mandatoryFields);
