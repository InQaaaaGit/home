/**
 * vim: ts=2 sw=2 sts=2
 * @module local_cdo_unti2035bas/local/modalform_factdef_edit
 */

import ModalForm from 'core_form/modalform';
import {get_string} from 'core/str';

class ModalFormFactdefEdit extends ModalForm {
  constructor(factdefid) {
    super({
      formClass: 'local_cdo_unti2035bas\\form\\factdef_edit',
      args: { factdefid },
    });
  }

  async show() {
    try {
      return await super.show();
    } finally {
      this.modal.setTitle(get_string('factdefedit', 'local_cdo_unti2035bas'));
    }
  }
}

export default ModalFormFactdefEdit;
