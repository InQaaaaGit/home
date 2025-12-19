/**
 * vim: et ts=2 sts=2 sw=2
 * @module     local_cdo_unti2035bas/streams_control
 */
/* eslint-disable jsdoc/require-jsdoc */

import * as DT from 'core_table/dynamic';
import ModalFormStreamEdit from 'local_cdo_unti2035bas/local/modalform_stream_edit';


function init(uniqid) {
  const root = document.querySelector(`[data-uniqid="${uniqid}"]`);
  const fieldset = root.querySelector('fieldset');
  const btnCreate = root.querySelector('.create-stream');
  let table = document.querySelector(`[data-table-uniqueid="${root.dataset.table}"]`);

  const show_modal = async(id = 0) => {
    const modal = new ModalFormStreamEdit(id);
    modal.addEventListener(modal.events.FORM_SUBMITTED, () => {
      DT.refreshTableContent(table);
    });
    await modal.show();
    return modal;
  };

  root.addEventListener('submit', event => event.preventDefault());
  btnCreate.addEventListener('click', () => show_modal());
  document.addEventListener(DT.Events.tableContentRefreshed, event => {
    if (event.target.dataset.tableUniqueid == root.dataset.table) {
      table = event.target;
    }
  });
  fieldset.disabled = false;
}

export default {
  init,
};
