
/**
 * Removes from the given text most non-alphanumeric characters.
 * @param text
 * @returns
 */
function removePunctuationMarks(text) {
	var cleanText = text.replace(/[\.,-\/#!$%\^&\*;:{}=\-`~()]/g, " ").replace(/\s+/g, " ");
	return cleanText.trim();
}
