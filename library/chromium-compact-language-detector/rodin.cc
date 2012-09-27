#include <stdio.h>
#include "encodings/compact_lang_det/compact_lang_det.h"
#include "encodings/compact_lang_det/ext_lang_enc.h"
#include "encodings/compact_lang_det/unittest_data.h"
#include "encodings/proto/encodings.pb.h"

int main(int argc, char **argv) {
    bool is_plain_text = true;
    bool do_allow_extended_languages = false;
    bool do_pick_summary_language = false;
    bool do_remove_weak_matches = false;
    bool is_reliable;
    Language plus_one = UNKNOWN_LANGUAGE;
    const char* tld_hint = NULL;
    int encoding_hint = UTF8;
    Language language_hint = UNKNOWN_LANGUAGE;

    double normalized_score3[3];
    Language language3[3];
    int percent3[3];
    int text_bytes;

	if (argc > 1) {
		const char* src = argv[1];
		
	    Language lang;
	    lang = CompactLangDet::DetectLanguage(0,
	                                          src, strlen(src),
	                                          is_plain_text,
	                                          do_allow_extended_languages,
	                                          do_pick_summary_language,
	                                          do_remove_weak_matches,
	                                          tld_hint,
	                                          encoding_hint,
	                                          language_hint,
	                                          language3,
	                                          percent3,
	                                          normalized_score3,
	                                          &text_bytes,
	                                          &is_reliable);

	    //printf("%s", LanguageCode(lang));

        // RODIN - If the detected language is one of the 3 we use in our service
        // then we accept and print it. Otherwise we check the whole 3 possibilities.
        //
        // NB. Not doing so is still not enough for "bankenkrise".
        const char* languageCode = LanguageCode(lang);
        
        if (strcmp(languageCode, "en") == 0 || strcmp(languageCode, "fr") == 0 || strcmp(languageCode, "de") == 0) {
          printf("%s", languageCode); 
        } else {
          bool ok = false;
        
          for (int i=0; i<3 && !ok; i++) {
            languageCode = LanguageCode(language3[i]);
        
            if (strcmp(languageCode, "en") == 0 || strcmp(languageCode, "fr") == 0 || strcmp(languageCode, "de") == 0) {
              printf("%s", languageCode); 
              ok = true;
            }
          }

          // Could be removed if we verify that the hint forces the languages to be on the list
          if (!ok) printf("un");
        }
        // RODIN - end
    }
}
