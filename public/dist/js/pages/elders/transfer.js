import { getDistricts } from '../members/ajax.js';
import { mandatoryFields, clearOnChange, validation } from '../utils/utils.js';
const congSelect = document.getElementById('congregation');
const districtSelect = document.getElementById('district');
const oldDistrict = document.getElementById('olddistrict');
const form = document.querySelector('form');

congSelect.addEventListener('change', async function (e) {
  if (!e.target.value || e.target.value === '') return;
  districtSelect.innerHTML = await getDistricts(e.target.value);
});

form.addEventListener('submit', e => {
  e.preventDefault();
  if (validation() > 0) return;

  if (+districtSelect.value === +oldDistrict.value) {
    districtSelect.classList.add('is-invalid');
    districtSelect.nextSibling.nextSibling.textContent =
      'Old and new districts are same';
  }

  document.form.submit();
});

clearOnChange(mandatoryFields);
