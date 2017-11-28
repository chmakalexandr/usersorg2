var messages = { en_US: {}};

messages.en_US.error = {
    file: {
        toobig: 'Mb\nToo big file!\nFile must be less than',
        xmlfile: 'Only XML files are allowed to upload',
        blank: 'Blank file!',
        upgrade: 'Please upgrade your browser, because your current browser lacks some new features we need!'
    }
};

module.exports = {
    messages: messages.en_US
};