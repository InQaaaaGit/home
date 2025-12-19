/**
 * vim: et ts=2 sts=2 sw=2
 * @module local_cdo_unti2035bas/utils
 */
/* eslint-disable jsdoc/require-jsdoc */

function decorate(method, decorator) {
  Object.defineProperty(this, method.name, {
    writable: false,
    value: decorator(method),
  });
}

export {
  decorate,
};
