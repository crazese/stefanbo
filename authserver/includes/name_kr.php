<?php 
function getFirstName() {
	$first = array('가', '각', '간', '갈', '감', '갑', '강', '개', '거', '건', '견', '결', '겸', '경', '계', '고', '곡', '곤', '공', '과', '곽','관','광','괴','교','구','국','궁','권','귀','규','균','근','금', '기', '길', '김', '나', '낙', '난', '남', '낭', '내', '노', '누', '능', '니', '다', '단', '달', '담', '당', '대', '도', '독', '돈', '동', '두', '등', '라', '락', '란', '람', '랑', '량', '려', '련', '렴', '령', '로', '록', '료', '룡', '루', '류', '륙', '리', '린', '림', '마', '만', '망', '매', '맹', '면', '명', '모', '목', '묘', '무', '묵', '문', '미', '민', '박', '반', '발', '방', '배', '백', '번', '범', '법', '벽', '변', '병', '보', '복', '봉', '부', '분', '불', '비', '빈', '빙', '사', '산', '살', '삼', '상', '새', '서', '석', '선', '설', '섭', '성', '세', '소', '속', '손', '송', '수', '숙', '순', '승', '시', '식', '신', '심', '씨', '아', '악', '안', '애', '앵', '야', '약', '양', '어', '엄', '여', '역', '연', '열', '염', '엽', '영', '예', '오', '옥', '온', '옹', '완', '왕', '요', '용', '우', '욱', '운', '원', '월', '위', '유', '육', '윤', '율', '은', '음', '응', '의', '이', '인', '일', '임', '자', '작', '잠', '장', '재', '저', '적', '전', '점', '정', '제', '조', '종', '좌', '주', '준', '중', '증', '지', '진', '질', '차', '찬', '참', '창', '채', '척', '천', '철', '첨', '첩', '청', '체', '초', '총', '최', '추', '축', '충', '취', '치', '침', '타', '탁', '탄', '태', '토', '투', '파', '판', '패', '팽', '편', '평', '폐', '포', '표', '풍', '피', '필', '하', '학', '한', '함', '항', '해', '행', '향', '허', '혁', '현', '협', '형', '혜', '호', '홍', '화', '환', '황', '회', '효', '후', '훈', '휘', '흉', '희'); 
	
	$body = array('가람', '가숙', '가원', '각균', '간란', '간수', '감심', '갑년', '갑동', '갑득', '갑련', '갑분', '갑생', '갑서', '갑성', '갑식', '갑연', '갑주', '갑태', '갑회', '갑휴', '갑희', '강건', '강국', '강남', '강백', '강수', '강순', '강언', '강열', '강옥', '강우', '강원', '강준', '강찬', '강태', '강한', '거영', '거월', '건겸', '건모', '건상', '건서', '건설', '건숙', '건식', '건예', '건오', '건욱', '건임', '건주', '건하', '건호', '건홍', '걸재', '게수', '결연', '경난', '경동', '경두', '경래', '경레', '경례', '경룡', '경문', '경민', '경배', '경섭', '경완', '경용', '경윤', '경임', '경제', '경주', '경준', '경채', '경철', '경환', '경효', '경훈', '경흥', '계분', '계석', '계순', '계승', '계오', '계옥', '계환', '고지', '고환', '곡례', '공덕', '공성', '공심', '공일', '관세', '관숙', '관열', '관욱', '관원', '관일', '관철', '관태', '관현', '관호', '관홍', '관희', '광남', '광대', '광돕', '광록', '광만', '광명', '광오', '광옥', '광운', '광제', '광조', '광지', '광표', '광한', '광희', '교리', '교림', '교빈', '교석', '교식', '교제', '교진', '교현', '구민', '구열', '구운', '구원', '구희', '국영', '국옥', '국중', '국진', '국한', '군식', '군자', '군철', '군표', '궁군', '궁근', '궁달', '궁제', '궁회', '권기', '권민', '권한', '귀래', '귀례', '귀월', '귀인', '귀정', '귀진', '귀철', '귀환', '규갑', '규덕', '규득', '규목', '규민', '규수', '규옥', '규웅', '규창', '규태', '규표', '규헌', '규현', '규횡', '규희', '균수', '균철', '그림', '근동', '근성', '근수', '근숙', '근순', '근양', '근우', '근욱', '근율', '근이', '근재', '근중', '근철', '근춘', '근탁', '근한', '근환', '금동', '금례', '금상', '금선', '금승', '금안', '금애', '금여', '금열', '금엽', '금예', '금옥', '금재', '금탄', '금태', '금행', '금혜', '기관', '기권', '기금', '기나', '기남', '기두', '기란', '기만', '기방', '기보', '기빈', '기열', '기오', '기웅', '기일', '기전', '기제', '기춘', '기출', '기치', '기평', '기화', '기효', '기희', '길려', '길례', '길복', '길숙', '길옥', '길인', '길중', '길형', '꽃님', '나엘', '나연', '나예', '낙순', '낙훈', '난경', '난민', '난옥', '난초', '난형', '남극', '남두', '남래', '남만', '남복', '남섭', '남실', '남안', '남욱', '남준', '남지', '남필', '남형', '남회', '낭아', '내규', '내식', '내원', '너미', '노라', '노성', '노순', '노윤', '노일', '노헌', '노혜', '노화', '노훈', '녹상', '눈희', '능자', '능주', '다빈', '다야', '다윤', '단덕', '단운', '달래', '달막', '달분', '달순', '달용', '달웅', '달화', '달훈', '대규', '대남', '대녀', '대락', '대범', '대섭', '대영', '대우', '대운', '대윤', '대은', '대진', '대호', '대홍', '대화', '더기', '덕구', '덕미', '덕분', '덕산', '덕상', '덕신', '덕애', '덕우', '덕유', '덕임', '덕중', '덕천', '덕행', '덕현', '덕효', '도관', '도균', '도금', '도생', '도석', '도선', '도수', '도숙', '도영', '도익', '도인', '도자', '도재', '도필', '도하', '도훈', '돈하', '동강', '동거', '동걸', '동교', '동국', '동군', '동래', '동문', '동복', '동봉', '동서', '동석', '동순', '동아', '동연', '동옥', '동완', '동임', '동조', '동철', '동해', '동혁', '동화', '동환', '동후', '두금', '두련', '두렬', '두상', '두생', '두선', '두순', '두열', '두옥', '두외', '두진', '두천', '두출', '두흠', '둘연', '득경', '득남', '득례', '득수', '득영', '등희', '딘이', '라헬', '란식', '란영', '란자', '란초', '란회', '랑남', '래언', '래창', '래형', '련경', '로사', '루지', '룰이', '리나', '리리', '마당', '막금', '막동', '막례', '막엽', '막자', '만덕', '만득', '만복', '만심', '만영', '만오', '만정', '만진', '말기', '말늠', '말백', '말선', '말휴', '매화', '맨선', '맹님', '맹숙', '맹옥', '맹자', '면순', '명가', '명갑', '명구', '명단', '명로', '명록', '명린', '명복', '명성', '명숙', '명승', '명예', '명우', '명윤', '명인', '명혁', '묘연', '묘옥', '묘택', '무경', '무생', '무수', '무순', '무연', '무원', '무임', '무칠', '무향', '문구', '문근', '문법', '문부', '문생', '문성', '문세', '문예', '문일', '문철', '문태', '문하', '문행', '문현', '뮤리', '미내', '미님', '미련', '미례', '미르', '미미', '미송', '미양', '미연', '미예', '미옥', '미자', '미해', '미형', '미혜', '미환', '미희', '민관', '민교', '민금', '민별', '민봉', '민용', '민우', '민욱', '민표', '민하', '민행', '민현', '민홍', '민훈', '민희', '방녀', '방순', '방희', '배길', '배선', '배웅', '배화', '백수', '백원', '백준', '버진', '범규', '범동', '범응', '범자', '범중', '범진', '법묵', '벙진', '병각', '병교', '병구', '병동', '병련', '병빈', '병생', '병수', '병연', '병용', '병이', '병철', '병하', '병학', '병훈', '보름', '보미', '보민', '보성', '보일', '보임', '보철', '보하', '보현', '보희', '복교', '복금', '복녀', '복만', '복명', '복묵', '복민', '복상', '복석', '복원', '복의', '복익', '복재', '복천', '복턱', '복현', '복화', '복활', '본문', '본민', '본상', '본석', '본성', '본애', '본홍', '봄이', '봉경', '봉군', '봉근', '봉기', '봉니', '봉상', '봉선', '봉세', '봉애', '봉업', '봉여', '봉예', '봉운', '봉원', '봉정', '봉주', '봉줄', '봉춘', '봉학', '봉호', '부심', '부연', '부한', '부헌', '부흥', '북호', '분악', '분연', '분용', '분의', '분출', '분호', '분화', '비올', '빈례', '사순', '사용', '사훈', '산옥', '산유', '산호', '삼곤', '삼단', '삼동', '삼모', '삼목', '삼서', '삼선', '삼식', '삼열', '삼용', '삼재', '삼호', '삼희', '상갑', '상건', '상관', '상권', '상근', '상명', '상모', '상민', '상벽', '상보', '상선', '상수', '상식', '상우', '상운', '상익', '상정', '상춘', '상태', '상헌', '상협', '상화', '상환', '상훈', '상휘', '상희', '새리', '새힘', '생섭', '생원', '서라', '서로', '서린', '서연', '서진', '서현', '서흥', '석건', '석경', '석관', '석교', '석구', '석권', '석균', '석남', '석대', '석도', '석례', '석미', '석분', '석상', '석승', '석식', '석열', '석영', '석예', '석온', '석운', '석윤', '석이', '석일', '석주', '석준', '석지', '석진', '석행', '석헌', '석희', '선공', '선균', '선극', '선만', '선명', '선모', '선복', '선상', '선생', '선순', '선식', '선언', '선엽', '선웅', '선원', '선익', '선준', '선철', '선호', '선희', '설진', '설현', '설화', '성건', '성경', '성구', '성년', '성도', '성동', '성두', '성록', '성문', '성범', '성복', '성술', '성식', '성실', '성오', '성용', '성우', '성욱', '성임', '성전', '성제', '성중', '성지', '성태', '성택', '성표', '성필', '성해', '성흠', '세교', '세권', '세례', '세민', '세병', '세복', '세비', '세순', '세아', '세용', '세운', '세철', '세택', '세행', '세휘', '소곤', '소도', '소라', '소란', '소련', '소미', '소석', '소선', '소양', '소은', '소자', '소점', '소제', '소중', '소진', '소화', '속순', '손도', '손선', '손희', '솔잎', '솔지', '송기', '송미', '송순', '송영', '송월', '송은', '송지', '송하', '수근', '수길', '수덕', '수돈', '수득', '수래', '수례', '수모', '수복', '수생', '수성', '수소', '수암', '수야', '수억', '수연', '수옥', '수우', '수원', '수윤', '수이', '수조', '수종', '수지', '수창', '수청', '수필', '수하', '수현', '수호', '수홍', '수회', '수훈', '숙구', '숙녀', '숙련', '숙선', '숙정', '순건', '순구', '순녀', '순늠', '순덕', '순도', '순동', '순란', '순매', '순명', '순배', '순범', '순봉', '순생', '순숙', '순실', '순아', '순오', '순우', '순일', '순자', '순전', '순좌', '순준', '순중', '순찬', '순창', '순초', '순호', '순화', '술선', '술연', '술이', '숭웅', '슬이', '승구', '승권', '승묵', '승빈', '승석', '승선', '승섭', '승수', '승심', '승아', '승업', '승열', '승오', '승종', '승주', '승철', '승혁', '승호', '승환', '승희', '시덕', '시몬', '시양', '시언', '시연', '시준', '시택', '시호', '신국', '신덕', '신목', '신배', '신섭', '신식', '신신', '신연', '신엽', '신완', '신의', '신일', '신정', '신혜', '신후', '신희', '쌍감', '쌍구', '쌍래', '쌍선', '쌍순', '쌍식', '쌍심', '쌍현', '쌍환', '아람', '아지', '안덕', '안순', '안용', '안희', '앙숙', '애덕', '애진', '야모', '양래', '양렬', '양묵', '양섭', '양수', '양애', '양욱', '양운', '양이', '양재', '양진', '양춘', '양평', '양필', '양하', '양현', '양희', '언희', '여솔', '여옥', '여일', '여진', '여현', '여훈', '역순', '연규', '연동', '연두', '연록', '연묵', '연선', '연섭', '연애', '연욱', '연원', '연응', '연자', '연중', '연진', '연표', '연호', '연환', '열모', '염순', '염이', '영갑', '영교', '영균', '영근', '영긴', '영남', '영덕', '영두', '영들', '영묵', '영본', '영봉', '영부', '영서', '영석', '영수', '영신', '영아', '영암', '영월', '영은', '영응', '영의', '영주', '영택', '영학', '영현', '영환', '예나', '예람', '예리', '예문', '예분', '예석', '예송', '예순', '예진', '예호', '오규', '오근', '오남', '오병', '오생', '오섭', '오센', '오연', '오유', '오임', '오종', '오춘', '오희', '옥귀', '옥규', '옥균', '옥근', '옥금', '옥녀', '옥대', '옥매', '옥봉', '옥선', '옥윤', '옥조', '옥채', '옥출', '옥현', '온덕', '온식', '완선', '완식', '완영', '완우', '완철', '왈영', '왕건', '외곤', '외대', '외분', '외임', '외재', '외종', '외주', '요단', '요한', '용갑', '용기', '용단', '용도', '용미', '용백', '용보', '용봉', '용비', '용상', '용식', '용연', '용옥', '용은', '용자', '용점', '용종', '용철', '용팔', '용하', '용학', '용호', '용환', '우동', '우모', '우수', '우숙', '우심', '우영', '우제', '우조', '우찬', '우천', '우친', '우택', '우향', '우홍', '우환', '욱균', '욱현', '운경', '운근', '운기', '운남', '운녕', '운이', '운주', '운준', '운집', '웅묵', '웅섭', '웅준', '웅천', '웅희', '원갑', '원경', '원대', '원덕', '원림', '원백', '원상', '원수', '원식', '원중', '원진', '원춘', '원판', '원학', '원형', '원홍', '원환', '원훈', '월구', '월례', '월상', '월선', '월희', '위선', '위숙', '유나', '유돈', '유동', '유례', '유림', '유복', '유상', '유승', '유영', '유찬', '유철', '유탁', '윤덕', '윤례', '윤생', '윤시', '윤영', '윤정', '윤지', '윤택', '윤하', '윤혹', '융부', '으뜸', '은각', '은경', '은균', '은기', '은년', '은단', '은록', '은민', '은방', '은배', '은분', '은상', '은샘', '은설', '은섭', '은숙', '은슬', '은승', '은식', '은심', '은아', '은예', '은용', '은이', '은임', '은장', '은중', '은진', '은철', '은태', '은표', '은하', '은학', '은해', '은회', '을남', '을님', '을령', '을선', '을율', '을학', '응균', '응순', '응식', '응주', '의길', '의례', '의수', '의숙', '의순', '의신', '의우', '의정', '의택', '이권', '이단', '이랑', '이성', '이순', '이슬', '이정', '이조', '이철', '이행', '이현', '이화', '익교', '익두', '익례', '익병', '익준', '인도', '인동', '인분', '인세', '인수', '인억', '인여', '인욱', '인웅', '인조', '인지', '인찬', '인탁', '인표', '인학', '인헌', '인혁', '인휴', '일겸', '일권', '일균', '일기', '일동', '일봉', '일산', '일섭', '일성', '일숙', '일식', '일완', '일점', '일태', '일하', '일한', '일호', '임순', '임영', '임원', '임조', '잉분', '자건', '자경', '자사', '자송', '자옥', '자은', '자종', '자헌', '자혜', '자홍', '자훈', '장근', '장대', '장도', '장문', '장민', '장배', '장오', '장은', '장학', '장현', '장환', '재경', '재고', '재규', '재단', '재독', '재란', '재룡', '재림', '재만', '재서', '재석', '재설', '재수', '재신', '재안', '재업', '재엽', '재오', '재용', '재우', '재익', '재임', '재정', '재철', '재출', '재표', '재필', '재학', '재한', '재환', '재훈', '전숙', '전순', '전훈', '점권', '점근', '점부', '점상', '점새', '점슬', '점연', '점원', '점윤', '점철', '점택', '점환', '정갑', '정국', '정권', '정귀', '정규', '정균', '정도', '정랑', '정린', '정무', '정묵', '정미', '정별', '정상', '정석', '정성', '정세', '정신', '정연', '정오', '정옥', '정음', '정인', '정일', '정재', '정제', '정창', '정칠', '정학', '정한', '정해', '정행', '정향', '정휘', '정휴', '정흠', '제극', '제림', '제숙', '제완', '제윤', '제진', '제형', '제호', '조애', '조운', '종건', '종경', '종귀', '종균', '종극', '종길', '종노', '종대', '종명', '종목', '종배', '종분', '종빈', '종사', '종생', '종서', '종선', '종숙', '종심', '종아', '종안', '종액', '종역', '종연', '종예', '종우', '종운', '종윤', '종은', '종이', '종주', '종진', '종짐', '종채', '종하', '종한', '종혁', '종현', '종협', '종형', '종홍', '종황', '종회', '종훈', '주민', '주순', '주아', '주안', '주애', '주언', '주역', '주용', '주월', '주찬', '주한', '주행', '주향', '주헌', '주화', '준규', '준남', '준대', '준동', '준명', '준민', '준병', '준봉', '준사', '준상', '준석', '준성', '준수', '준언', '준엄', '준열', '준원', '준재', '준지', '준택', '준표', '준학', '준한', '준환', '중규', '중길', '중배', '중보', '중상', '중순', '중웅', '중윤', '중하', '중혁', '중현', '쥬리', '증순', '증희', '지만', '지면', '지배', '지서', '지순', '지슬', '지승', '지유', '지은', '지황', '진강', '진건', '진경', '진국', '진규', '진근', '진녀', '진동', '진두', '진득', '진리', '진모', '진상', '진세', '진숙', '진양', '진의', '진춘', '진출', '진하', '진혁', '진후', '진휘', '차금', '차노', '차조', '차헌', '찬군', '찬균', '찬길', '찬민', '찬수', '찬에', '찬용', '찬웅', '찬윤', '찬임', '찬재', '찬종', '찬주', '찬한', '찬호', '창도', '창돈', '창란', '창림', '창만', '창문', '창복', '창상', '창석', '창선', '창순', '창언', '창여', '창용', '창위', '창의', '창하', '창학', '창행', '창화', '채근', '채선', '채식', '채용', '채진', '채호', '채화', '처단', '척수', '천규', '천두', '천석', '천섭', '천세', '천예', '천우', '천위', '천은', '천자', '천탁', '천호', '철구', '철권', '철근', '철난', '철남', '철만', '철상', '철완', '철웅', '철원', '철자', '철형', '철희', '청균', '청용', '청은', '청일', '청훈', '초야', '초옥', '초자', '추연', '춘규', '춘녀', '춘단', '춘닭', '춘도', '춘미', '춘복', '춘분', '춘상', '춘석', '춘섭', '춘아', '춘악', '춘영', '춘우', '춘원', '춘응', '춘자', '춘종', '출구', '출식', '충건', '충구', '충규', '충모', '충배', '충옥', '충용', '충호', '충후', '충훈', '치동', '치석', '치숙', '치현', '칠구', '칠련', '칠봉', '칠우', '칠원', '칠한', '쾌동', '쾌준', '쾌호', '타관', '타식', '태건', '태관', '태금', '태량', '태록', '태만', '태목', '태민', '태분', '태석', '태선', '태승', '태식', '태양', '태언', '태열', '태온', '태왕', '태윈', '태율', '태은', '태응', '태일', '태중', '태진', '태화', '택규', '택임', '택환', '택희', '판갑', '판국', '판병', '판심', '판용', '판임', '판출', '팔균', '팔남', '팔매', '평구', '평근', '평순', '평안', '평원', '평화', '표두', '풍석', '풍신', '필구', '필규', '필능', '필련', '필석', '필숙', '필원', '하란', '하람', '하린', '하묵', '하선', '하식', '하얀', '하정', '하회', '학덕', '학도', '학래', '학배', '학범', '학석', '학선', '학섭', '학수', '학신', '학언', '학원', '학윤', '학조', '학칠', '학한', '학혁', '학현', '한경', '한곤', '한교', '한규', '한균', '한만', '한백', '한분', '한석', '한소', '한솔', '한수', '한순', '한신', '한영', '한용', '한웅', '한종', '한직', '한진', '한태', '한표', '한호', '항국', '항만', '항빈', '항수', '항자', '항재', '항휘', '해경', '해광', '해구', '해권', '해금', '해기', '해길', '해동', '해룡', '해문', '해봉', '해사', '해송', '해안', '해업', '해연', '해욱', '해조', '해주', '해필', '행규', '행림', '행순', '행윤', '행자', '행찰', '행희', '향난', '향도', '향수', '향애', '향월', '향진', '향태', '헌광', '헌묵', '헌순', '헌양', '헌영', '헌옥', '헌우', '헌재', '헌지', '헤경', '혀주', '혁균', '혁기', '혁노', '혁분', '혁숙', '혁식', '혁운', '혁주', '혁중', '혁진', '혁찬', '혁희', '현광', '현구', '현남', '현리', '현민', '현발', '현선', '현송', '현식', '현옹', '현용', '현이', '현임', '현학', '현향', '현호', '현회', '형경', '형노', '형록', '형모', '형빈', '형선', '형수', '형순', '형식', '형욱', '형을', '형임', '형자', '형정', '형지', '형창', '형학', '혜경', '혜덕', '혜란', '혜령', '혜륜', '혜린', '혜림', '혜민', '혜빈', '혜선', '혜승', '혜은', '혜이', '호균', '호근', '호금', '호기', '호대', '호래', '호선', '호송', '호수', '호신', '호이', '호익', '호임', '호재', '호현', '호형', '홈열', '홍림', '홍범', '홍상', '홍선', '홍숙', '홍순', '홍식', '홍영', '홍옥', '홍원', '홍제', '홍중', '홍패', '홍현', '홍화', '화경', '화계', '화림', '화분', '화순', '화열', '화옥', '화임', '화자', '화종', '화지', '화춘', '화평', '화현', '환갑', '환구', '환균', '환동', '환묵', '환석', '환수', '환영', '환이', '환준', '환희', '활란', '활수', '황기', '황묵', '황석', '황식', '황철', '황회', '황휘', '회동', '회영', '회창', '횡자', '효곤', '효관', '효권', '효길', '효나', '효도', '효돈', '효배', '효빈', '효섭', '효심', '효연', '효용', '효임', '효진', '효창', '후봉', '훈상', '훈재', '훈택', '훈한', '휘석', '휘찬', '흐웅', '흥길', '흥래', '흥례', '흥록', '흥세', '흥수', '흥식', '흥양', '흥종', '흥한', '흥환', '희갑', '희계', '희남', '희댁', '희란', '희문', '희미', '희배', '희서', '희석', '희선', '희성', '희송', '희숙', '희시', '희안', '희애', '희연', '희옥', '희우', '희운', '희윈', '희종', '희준', '희중', '희채');
	$firstKey = array_rand($first,1);	
	$bodyKey = array_rand($body,1);
	return $first[$firstKey].$body[$bodyKey];
}
?>