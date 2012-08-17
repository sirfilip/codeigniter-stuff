;CI = {};

CI.index_file = 'index.php';

CI._base_url = 'http://example.com/';

CI.base_url = function(path) {
    return this._base_url + path;
};
CI.site_url = function(path) {
    prefix = this.index_file ? 'index.php/' : '';
    return this.base_url(prefix + path);
};
CI.redirect_to = function(path) {
    window.location.href = this.site_url(path);
};
CI.registry = {};

CI.STATUS_SUCCESS = 1;
CI.STATUS_ERROR = 2;
