async function performAction(name, payload) {
    if (payload === undefined || payload === null) {
        payload = {};
    }

    let response = await fetch('/api.php?action=' + name, {
       method: 'POST',
       body: JSON.stringify(payload),
       headers: {
           'Content-Type': 'application/json'
       }
   });

    return await response.json();
}
