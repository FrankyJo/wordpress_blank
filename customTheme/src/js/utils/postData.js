const postData = async (url = '', data = '') => {
    // Default options are marked with *
    const response = await fetch(url, {
        method: 'POST', // *GET, POST, PUT, DELETE, etc.
        mode: 'cors', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        redirect: 'follow', // manual, *follow, error
        referrerPolicy: 'no-referrer-when-downgrade', /* no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin,
    same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url */
        body: data, // body data type must match "Content-Type" header (if application/json - JSON.stringify(data))
    });

    if (response.ok) {
        return response.json();
    }

    return Promise.reject(response);
};

export default postData;
